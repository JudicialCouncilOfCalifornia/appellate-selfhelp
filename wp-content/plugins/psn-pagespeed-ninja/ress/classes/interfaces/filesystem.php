<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * FileSystem interface
 */
interface IRessio_Filesystem
{
    /**
     * Check file exists
     * @param string $filename
     * @return bool
     */
    public function isFile($filename);

    /**
     * Check directory exists
     * @param string $path
     * @return bool
     */
    public function isDir($path);

    /**
     * Get file size
     * @param string $filename
     * @return integer|bool
     */
    public function size($filename);

    /**
     * Load content from file
     * @param string $filename
     * @return string
     */
    public function getContents($filename);

    /**
     * Save content to file
     * @param string $filename
     * @param string $content
     * @return bool
     */
    public function putContents($filename, $content);

    /**
     * Make directory
     * @param string $path
     * @param int $chmod
     * @return bool
     */
    public function makeDir($path, $chmod = 0777);

    /**
     * Get file timestamp
     * @param string $path
     * @return int
     */
    public function getModificationTime($path);

    /**
     * Update file timestamp
     * @param string $filename
     * @param int $time
     * @return bool
     */
    public function touch($filename, $time = null);

    /**
     * Delete file or empty directory
     * @param string $path
     * @return bool
     */
    public function delete($path);

    /**
     * Copy file
     * @param string $src
     * @param string $target
     * @return bool
     */
    public function copy($src, $target);
}