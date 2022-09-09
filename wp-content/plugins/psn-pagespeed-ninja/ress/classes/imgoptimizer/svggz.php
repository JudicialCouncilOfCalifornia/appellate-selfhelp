<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * Images minification using GD
 */
class Ressio_ImgOptimizer_SvgGz extends Ressio_ImgOptimizer
{
    /**
     * @param $src_imagepath string
     * @return bool
     * @throws ERessio_UnknownDiKey
     */
    public function run($src_imagepath)
    {
        $src_ext = pathinfo($src_imagepath, PATHINFO_EXTENSION);
        if ($src_ext !== 'svg') {
            return false;
        }

        $fs = $this->di->filesystem;

        if (!$fs->isFile($src_imagepath)) {
            return false;
        }

        $src_timestamp = $fs->getModificationTime($src_imagepath);

        $pos = strrpos($src_imagepath, '.');
        $gz_imagepath = substr($src_imagepath, 0, $pos) . '.svgz';

        $gzExists = $fs->isFile($gz_imagepath);
        if ($gzExists && $src_timestamp === $fs->getModificationTime($gz_imagepath)) {
            return true;
        }

        $content = $fs->getContents($src_imagepath);
        $ret = $fs->putContents($gz_imagepath, gzencode($content, 9));
        if ($ret) {
            $fs->touch($gz_imagepath, $src_timestamp);
        }

        if (!$gzExists) {
            parent::saveDeleteRollback($gz_imagepath);
        }

        return $ret;
    }
}