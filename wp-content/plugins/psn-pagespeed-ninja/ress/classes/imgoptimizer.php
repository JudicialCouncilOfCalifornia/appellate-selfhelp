<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_ImgOptimizer implements IRessio_ImgOptimizer
{
    /** @var Ressio_DI */
    protected $di;
    /** @var Ressio_Config */
    public $config;

    public $logFile = '/imgoptimizer.log';

    /**
     * @param $di Ressio_DI
     * @throws ERessio_UnknownDiKey
     */
    public function setDI($di)
    {
        $this->di = $di;
        $this->config = $di->config;
    }

    /**
     * @param $srcFile string
     * @return bool
     */
    public function run($srcFile)
    {
        if (!is_file($srcFile)) {
            return false;
        }

        $ext = strtolower(pathinfo($srcFile, PATHINFO_EXTENSION));
        if ($ext === 'jpeg') {
            $ext = 'jpg';
        }
        /** @var IRessio_ImgOptimizer $imgOptimizer */
        $imgOptimizer = null;
        try {
            $imgOptimizer = $this->di->get('imgOptimizer.' . $ext);
        } catch (ERessio_UnknownDiKey $e) {
        }
        if ($imgOptimizer) {
            return $imgOptimizer->run($srcFile);
        }
        return false;
    }

    /**
     * @param $src_imagepath string
     * @param $src_timestamp int
     * @param $backup_imagepath string
     * @throws ERessio_UnknownDiKey
     */
    public function backup($src_imagepath, $src_timestamp, $backup_imagepath)
    {
        // @todo backup() should return success status of copy() method
        // (to don't allow Ressio_ImgOptimizer_Exec to delete the file

        $fs = $this->di->filesystem;

        // @todo add filelock?

        $backup_file_exists = $fs->isFile($backup_imagepath);

        $fs->copy($src_imagepath, $backup_imagepath);
        $fs->touch($backup_imagepath, $src_timestamp);

        if (!$backup_file_exists) {
            $this->saveMoveRollback($src_imagepath, $backup_imagepath);
        }
    }

    /**
     * @param string $orig_path
     * @param string $backup_path
     */
    public function saveMoveRollback($orig_path, $backup_path)
    {
        // @todo implement appendContent() method in IRb_FileSystem
        file_put_contents(RESSIO_PATH . $this->logFile, "\"$orig_path\"=\"$backup_path\"\n", FILE_APPEND | LOCK_EX);
    }

    /**
     * @param string $file_path
     */
    public function saveDeleteRollback($file_path)
    {
        file_put_contents(RESSIO_PATH . $this->logFile, "\"\"=\"$file_path\"\n", FILE_APPEND | LOCK_EX);
    }

    /**
     * @return bool
     * @throws ERessio_UnknownDiKey
     */
    public function restore()
    {
        $fs = $this->di->filesystem;

        $logFilename = RESSIO_PATH . $this->logFile;
        if (!$fs->isFile($logFilename)) {
            return true;
        }

        $ok = true;
        $size = $fs->size($logFilename);
        $log = explode("\n", $fs->getContents($logFilename));
        foreach ($log as $srcdest) {
            if (preg_match('#"(.*?)"="(.*?)"#', $srcdest, $matches)) {
                $image_backup = $matches[2];
                if (!$fs->isFile($image_backup)) {
                    continue;
                }

                $image = $matches[1];
                if ($image === '') {
                    // Delete rollback
                    $fs->delete($image_backup);
                } else {
                    // Copy rollback
                    $timeStamp_image = $fs->getModificationTime($image);
                    $timeStamp_backup = $fs->getModificationTime($image_backup);
                    if ($timeStamp_image === $timeStamp_backup) {
                        if ($fs->copy($image_backup, $image)) {
                            $fs->touch($image, $timeStamp_backup);
                            $fs->delete($image_backup);
                        } else {
                            // keep backup file
                            $ok = false;
                        }
                    } else {
                        $fs->delete($image_backup);
                    }
                }
            }
        }

        if ($ok && $fs->size($logFilename) === $size) {
            $fs->delete($logFilename);
        }

        return $ok;
    }
}
