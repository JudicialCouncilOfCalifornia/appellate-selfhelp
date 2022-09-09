<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2019 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Filesystem_Native implements IRessio_Filesystem
{
    /**
     * Check file exists
     * @param string $filename
     * @return bool
     */
    public function isFile($filename)
    {
        return is_file($filename);
    }

    /**
     * Check directory exists
     * @param string $path
     * @return bool
     */
    public function isDir($path)
    {
        return is_dir($path);
    }

    /**
     * @param string $filename
     * @return integer|bool
     */
    public function size($filename)
    {
        return filesize($filename);
    }

    /**
     * Load content from file
     * @param string $filename
     * @return string
     */
    public function getContents($filename)
    {
        return @file_get_contents($filename);
    }

    /**
     * Save content to file
     * @param string $filename
     * @param string $content
     * @return bool
     */
    public function putContents($filename, $content)
    {
        $size = strlen($content);
        $dir = dirname($filename);

        // inherit permissions for new files
        $mode = file_exists($filename) ? @fileperms($filename) : (@fileperms($dir) & 0666);

        $return = true;
        // save to a temporary file and do atomic update via rename
        $tmp = tempnam($dir, basename($filename));
        if (
            (file_put_contents($tmp, $content, LOCK_EX) !== $size) ||
            !rename($tmp, $filename)
        ) {
            // otherwise try to overwrite directly
            @unlink($tmp);
            $return = (file_put_contents($filename, $content, LOCK_EX) === $size);
        }

        return @chmod($filename, $mode) && $return;
    }

    /**
     * Make directory
     * @param string $path
     * @param int $chmod
     * @return bool
     */
    public function makeDir($path, $chmod = 0777)
    {
        return is_dir($path) || @mkdir($path, $chmod, true) || is_dir($path);
    }

    /**
     * Get file timestamp
     * @param string $path
     * @return int
     */
    public function getModificationTime($path)
    {
        $time = @filemtime($path);
        // @todo remove this bugfix [required for PHP 5.2 on Windows only]
        // @todo Note: seems it is fixed for NTFS and not FAT (see https://bugs.php.net/bug.php?id=40568)
        if ((float)PHP_VERSION < 5.3 && $time !== false && strncasecmp(PHP_OS, 'win', 3) === 0) {
            // fix filemtime on Windows
            $time += 3600 * (date('I') - date('I', $time));
        }
        return $time;
    }

    /**
     * Update file timestamp
     * @param string $filename
     * @param int $time
     * @return bool
     */
    public function touch($filename, $time = null)
    {
        if ($time === null) {
            // Note: null is processed as 0 by touch()
            $time = time();
        }
        return touch($filename, $time);
    }

    /**
     * Delete file or empty directory
     * @param string $path
     * @return bool
     */
    public function delete($path)
    {
        return unlink($path);
    }

    /**
     * Copy file
     * @param string $src
     * @param string $target
     * @return bool
     */
    public function copy($src, $target)
    {
        return copy($src, $target);
    }
}