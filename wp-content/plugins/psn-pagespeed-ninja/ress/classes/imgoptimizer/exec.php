<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * Images minification using external executables
 */
class Ressio_ImgOptimizer_Exec extends Ressio_ImgOptimizer
{
    public $supported_exts = array('bmp', 'gif', 'ico', 'jpg', 'jpeg', 'png', 'svg', 'svgz', 'tif', 'tiff', 'webp');

    public function __construct()
    {
    }

    /**
     * @param $src_imagepath string
     * @return bool
     * @throws ERessio_UnknownDiKey
     */
    public function run($src_imagepath)
    {
        $src_ext = pathinfo($src_imagepath, PATHINFO_EXTENSION);
        if (!in_array($src_ext, $this->supported_exts, true)) {
            return false;
        }

        $fs = $this->di->filesystem;

        if (!$fs->isFile($src_imagepath)) {
            return false;
        }

        $src_timestamp = $fs->getModificationTime($src_imagepath);

        $orig_imagepath = $src_imagepath . $this->config->img->origsuffix;
        if ($src_timestamp === $fs->getModificationTime($orig_imagepath)) {
            return true;
        }

        parent::backup($src_imagepath, $src_timestamp, $orig_imagepath);

        if (isset($this->config->img->execoptim->$src_ext)) {
            $command = $this->config->img->execoptim->$src_ext;

            $command = str_replace('$filename', escapeshellarg($src_imagepath), $command);
            exec($command, $output, $retval);

            unset($output);
            $src_filesize = $fs->size($orig_imagepath);
            $new_filesize = $fs->size($src_imagepath);
            if ($retval !== 0 || $new_filesize === 0 || $new_filesize >= $src_filesize) {
                $fs->delete($src_imagepath);
                $fs->copy($orig_imagepath, $src_imagepath);
            }
            $fs->touch($src_imagepath, $src_timestamp);
        }

        return true;
    }
}