<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Cache_File implements IRessio_Cache
{
    /** @var Ressio_DI */
    private $di;
    /** @var IRessio_Filesystem */
    private $fs;
    /** @var IRessio_FileLock */
    private $filelock;
    /** @var string */
    private $cachedir;
    /** @var int */
    private $time;
    /** @var int */
    private $aging_time;
    /** @var int Update time is used to don't update filesystem at initial 10% of cache lifetime */
    private $update_time;
    /** @var string */
    private $prefix = '';
    /** @var array */
    private $id2file = array();

    /**
     * @param $dir string
     * @param $ttl int
     */
    public function __construct($dir = './cache', $ttl = null)
    {
        $this->cachedir = $dir;
        $this->time = time();
        $ttl_value = ($ttl === null ? 24 * 60 * 60 : $ttl);
        $this->aging_time = $this->time - $ttl_value;
        $this->update_time = $this->time - 0.9*$ttl_value;
    }

    /**
     * @param $di Ressio_DI
     * @throws ERessio_UnknownDiKey
     */
    public function setDI($di)
    {
        $this->di = $di;
        $this->fs = $di->filesystem;
        $this->filelock = $di->filelock;
        $config = $di->config;
        if (isset($config->cachedir)) {
            $this->cachedir = $config->cachedir;
        }
        if (isset($config->cachettl)) {
            $this->aging_time = $this->time - $config->cachettl;
            $this->update_time = $this->time - 0.9*$config->cachettl;
        }
        $this->prefix = $this->fs->getModificationTime(__FILE__) . '_';
        if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'], 'off') !== 0)) {
            $this->prefix .= 'https:';
        }
        $this->prefix .= $_SERVER['HTTP_HOST'] . '_';
    }

    /**
     * @param $deps string|array
     * @param $suffix string
     * @return string
     */
    public function id($deps, $suffix = '')
    {
        if (is_array($deps)) {
            $deps = implode("\0", $deps);
        }
        return sha1($this->prefix . $deps) . '_' . $suffix;
    }

    /**
     * @param $id string
     * @return string|bool
     */
    public function getOrLock($id)
    {
        $filename = $this->fileById($id);
        if ($this->fs->isFile($filename)) {
            $mtime = $this->fs->getModificationTime($filename);
            if ($mtime <= $this->aging_time && $mtime > $this->update_time) {
                // update modification time to don't remove actively used cache file
                $this->fs->touch($filename);
            }
            return $this->fs->getContents($filename);
        }
        return $this->filelock->lock($filename);
    }

    /**
     * @param $id string
     * @return bool
     */
    public function lock($id)
    {
        $filename = $this->fileById($id);
        return $this->filelock->lock($filename);
    }

    /**
     * @param $id string
     * @param $data string
     * @return bool
     */
    public function storeAndUnlock($id, $data)
    {
        $filename = $this->fileById($id);
        if ($this->filelock->isLocked($filename, true)) {
            $this->fs->putContents($filename, $data);
            $this->filelock->unlock($filename);
            return true;
        }
        return false;
    }

    /**
     * @param $id string
     * @return bool
     */
    public function delete($id)
    {
        $filename = $this->fileById($id);
        if (!$this->fs->isFile($filename)) {
            return true;
        }
        if (!$this->filelock->lock($filename)) {
            return false;
        }
        $this->fs->delete($filename);
        $this->filelock->unlock($filename);
        return true;
    }

    /**
     * @param $id string
     * @return string
     */
    private function fileById($id)
    {
        if (!isset($this->id2file[$id])) {
            $dir = $this->cachedir . '/' . substr($id, 0, 2);
            $this->fs->makeDir($dir);
            $this->id2file[$id] = $dir . '/' . $id;
        }
        return $this->id2file[$id];
    }
}
