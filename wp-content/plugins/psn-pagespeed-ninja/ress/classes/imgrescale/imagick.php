<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * Images scale down minification using ImageMagick
 */
class Ressio_ImgRescale_Imagick implements IRessio_ImgRescale
{
    public $supported_exts = array('jpg', 'gif', 'png');
    //public $thumbdir = 'imgcache';

    /** @var Ressio_DI */
    private $di;

    public function __construct()
    {
        if (!extension_loaded('imagick')) {
            throw new ERessio_Exception('ImageMagick extension is not loaded.');
        }
        if (count(Imagick::queryFormats('webp'))) {
            $this->supported_exts[] = 'webp';
        }
    }

    /**
     * @param Ressio_DI $di
     */
    public function setDI($di)
    {
        $this->di = $di;
    }

    /**
     * @return array
     */
    public function getSupportedExts()
    {
        return $this->supported_exts;
    }

    /**
     * Rescale Image
     * @param string $src_imagepath
     * @param string $dest_imagepath
     * @param int $dest_width
     * @param int $dest_height
     * @param string|bool $dest_ext
     * @return string
     * @throws ERessio_UnknownDiKey
     */
    public function rescale($src_imagepath, $dest_imagepath, $dest_width, $dest_height, $dest_ext = false)
    {
        $config = $this->di->config;

        $jpegquality = $config->img->jpegquality;
        $src_ext = strtolower(pathinfo($src_imagepath, PATHINFO_EXTENSION));
        if ($src_ext === 'jpeg') {
            $src_ext = 'jpg';
        }

        if (!in_array($src_ext, $this->supported_exts, true)) {
            return $src_imagepath;
        }

        $fs = $this->di->filesystem;

        $src_mtime = $fs->getModificationTime($src_imagepath);
        if ($fs->isFile($dest_imagepath)) {
            $dest_mtime = $fs->getModificationTime($dest_imagepath);
            if ($src_mtime === $dest_mtime) {
                return $dest_imagepath;
            }
        }

        $dest_imagedir = dirname($dest_imagepath);
        if (!$fs->isDir($dest_imagedir)) {
            $fs->makeDir($dest_imagedir);
            $indexhtml = '<html><body bgcolor="#FFFFFF"></body></html>';
            $fs->putContents($dest_imagedir . '/index.html', $indexhtml);
        }

        // @todo add filelock?

        if (!$fs->copy($src_imagepath, $dest_imagepath)) {
            return $src_imagepath;
        }

        try {
            $dest_image = new Imagick($dest_imagepath);
        } catch (Exception $e) {
            $fs->delete($dest_imagepath);
            return $src_imagepath;
        }

        $fs->delete($dest_imagepath);

        try {

            if ($dest_image->getImageIterations()) {
                // animated gif
                return $src_imagepath;
            }

            $dest_image->setImageFormat($dest_ext);
            $dest_image->stripImage();
            $dest_image->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
            $dest_image->setBackgroundColor(new ImagickPixel('transparent'));

            $dest_image->resizeImage($dest_width, $dest_height, Imagick::FILTER_LANCZOS, 0.9);

            switch ($dest_ext) {
                case 'jpg':
                    $dest_image->setImageCompression(Imagick::COMPRESSION_JPEG);
                    $dest_image->setImageCompressionQuality($jpegquality);
                    $dest_image->setInterlaceScheme(Imagick::INTERLACE_PLANE);
                    break;
                case 'png':
                    $dest_image->setImageCompressionQuality(95);
                    $dest_image->setOption('png:compression-strategy', 1);
                    break;
            }

            $data = $dest_image->getImageBlob();
            $dest_image->clear();

        } catch (ImagickException $e) {
            $this->di->logger->warning('Catched error in Ressio_ImgRescale_Imagick::rescale: ' . $e->getMessage());
            return $src_imagepath;
        }

        $fs->putContents($dest_imagepath, $data);

        // @todo extract saveDeleteRollback and saveMoveRollback into separate interface
        $imgOptimizer = $this->di->imgOptimizer;
        if (method_exists($imgOptimizer, 'saveDeleteRollback')) {
            $imgOptimizer->saveDeleteRollback($dest_imagepath);
        }

        if ($config->img->minifyrescaled && $imgOptimizer) {
            $imgOptimizer->run($dest_imagepath);
        }

        $fs->touch($dest_imagepath, $src_mtime);

        return $dest_imagepath;
    }
}