<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2019 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * Images minification using GD
 */
class Ressio_ImgOptimizer_GD extends Ressio_ImgOptimizer
{
    public $supported_exts = array('jpg', 'gif', 'png');

    /** @var bool */
    protected $open_basedir_enabled;

    public function __construct()
    {
        // support broken JPEGs
        ini_set('gd.jpeg_ignore_warning', 1);
        // Support of WebP images in PHP 5.5+
        if (function_exists('imagecreatefromwebp') && function_exists('imagewebp')) {
            $this->supported_exts[] = 'webp';
        }

        $open_basedir = ini_get('open_basedir');
        $this->open_basedir_enabled = !empty($open_basedir);
    }

    /**
     * @param $src_imagepath string
     * @return bool
     * @throws ERessio_UnknownDiKey
     */
    public function run($src_imagepath)
    {
        // @todo extract common code from gd and exec optimizers to an abstract base class

        $src_ext = pathinfo($src_imagepath, PATHINFO_EXTENSION);
        if ($src_ext === 'jpeg') {
            $src_ext = 'jpg';
        }

        if (!in_array($src_ext, $this->supported_exts, true)) {
            return false;
        }

        $fs = $this->di->filesystem;

        if (!$fs->isFile($src_imagepath)) {
            return false;
        }

        $src_filesize = $fs->size($src_imagepath);

        // @todo skip optimization of small files [performance]

        $src_timestamp = $fs->getModificationTime($src_imagepath);

        $orig_imagepath = $src_imagepath . $this->config->img->origsuffix;
        if ($fs->isFile($orig_imagepath) && $src_timestamp === $fs->getModificationTime($orig_imagepath)) {
            return true;
        }

        $src_imagesize = getimagesize($src_imagepath);
        if ($src_imagesize === false) {
            return false;
        }
        if (function_exists('memory_get_usage')) {
            $max_mem_size = ini_get('memory_limit');
            if ($max_mem_size < 0) {
                $max_mem_size = '2G';
            }
            $max_mem_size = $this->size2int($max_mem_size);
            $src_image_mem_size = 8300 + 6 * $src_imagesize[0] * $src_imagesize[1];
            $extra_mem_size = 16 * 1024 + 2 * filesize($src_imagepath); // variables and other things
            $required_mem_size = memory_get_usage() + $src_image_mem_size + $extra_mem_size;
            if ($required_mem_size > $max_mem_size) {
                // @todo optimally allow to increase memory_limit up to a specified value
                // @todo (and restore memory_limit at the end of function)
                //ini_set('memory_limit', $required_mem_size);
                return false;
            }
        }

        parent::backup($src_imagepath, $src_timestamp, $orig_imagepath);

        $src_image = false;
        switch ($src_ext) {
            case 'jpg':
                $src_image = imagecreatefromjpeg($src_imagepath);
                break;
            case 'gif':
                $content = $fs->getContents($src_imagepath);
                if ($this->is_gif_ani($content)) {
                    return false;
                }
                $src_image = imagecreatefromstring($content);
                unset($content);
                break;
            case 'png':
                $src_image = imagecreatefrompng($src_imagepath);
                if ($src_image === false) {
                    // try restore image
                    $content = $fs->getContents($src_imagepath);
                    $png_header = chr(137) . "PNG\r\n" . chr(26) . "\n";
                    $png_end_chunk = 'IEND';
                    if (strncmp($content, $png_header, 8) === 0 && strpos($content, $png_end_chunk) === false) {
                        $content .= $png_end_chunk;
                        $src_image = imagecreatefromstring($content);
                    }
                    unset($content);
                }
                break;
            case 'webp':
                $src_image = imagecreatefromwebp($src_imagepath);
                break;
        }

        if ($src_image === false) {
            return false;
        }

        imagesavealpha($src_image, true);

        $tmp_dir = sys_get_temp_dir();
        if ($this->open_basedir_enabled || !is_writable($tmp_dir)) {
            $tmp_dir = dirname($src_imagepath);
        }
        if (!is_writable($tmp_dir)) {
            return false;
        }
        $tmp_filename = tempnam($tmp_dir, 'Ressio');
        $data = false;
        switch ($src_ext) {
            case 'jpg':
                imageinterlace($src_image, true);
                // @todo check return values of image<format> functions
                imagejpeg($src_image, $tmp_filename, $this->di->config->img->jpegquality);
                $data = file_get_contents($tmp_filename);
                $data = $this->jpeg_clean($data);
                break;
            case 'gif':
                imagegif($src_image, $tmp_filename);
                break;
            case 'png':
                if (version_compare(PHP_VERSION, '5.1.3', '>=')) {
                    imagepng($src_image, $tmp_filename, 9, PNG_ALL_FILTERS);
                } elseif (version_compare(PHP_VERSION, '5.1.2', '>=')) {
                    imagepng($src_image, $tmp_filename, 9);
                } else {
                    imagepng($src_image, $tmp_filename);
                }
                break;
            case 'webp':
                imagewebp($src_image, $tmp_filename, $this->di->config->img->jpegquality);
                break;
        }
        imagedestroy($src_image);
        if ($data === false) {
            $data = file_get_contents($tmp_filename);
        }
        unlink($tmp_filename);

        // @todo check $data is not empty
        if (strlen($data) >= $src_filesize) {
            return false;
        }

        $ret = $fs->putContents($src_imagepath, $data);
        $fs->touch($src_imagepath, $src_timestamp);
        return $ret;
    }

    /**
     * Remove JFIF and Comment headers from GD2-generated jpeg (saves 79 bytes)
     * @param string $jpeg_src
     * @return bool|string
     */
    private function jpeg_clean($jpeg_src)
    {
        // @todo extract to gd utils (shares code with imgrescale.php)
        // Start of Image (SOI)
        $jpeg_clr = "\xFF\xD8";
        if (strncmp($jpeg_src, $jpeg_clr, 2) !== 0) {
            return false;
        }
        $pos = 2;
        $size = strlen($jpeg_src);
        while ($pos < $size) {
            if ($jpeg_src[$pos] !== "\xFF") {
                return false;
            }
            $b = $jpeg_src[$pos + 1];
            if ($b === "\xDA") {
                // Start of Scan (SOS)
                return $jpeg_clr . substr($jpeg_src, $pos);
            }
            $len = unpack('n', substr($jpeg_src, $pos + 2, 2));
            $len = array_shift($len);
            if ($b !== "\xE0" && $b !== "\xFE") {
                // not [Application Field 0 (APP0) ||  Comment (COM)]
                $jpeg_clr .= substr($jpeg_src, $pos, $len + 2);
            }
            $pos += $len + 2;
        }
        return false;
    }

    /**
     * Count animation frames in gif file, return TRUE if two or more
     * @param string $content
     * @return bool
     */
    private function is_gif_ani($content)
    {
        $count = preg_match_all('#\x00\x21\xF9\x04.{4}\x00[\x2C\x21]#s', $content, $matches);
        return $count > 1;
    }

    /**
     * Convert string presentation of memory size to integer
     * @param string $str
     * @return int
     */
    private function size2int($str)
    {
        $unit = strtoupper(substr($str, -1));
        $num = (int)substr($str, 0, -1);
        switch ($unit) {
            case 'G':
                $num *= 1024;
            case 'M':
                $num *= 1024;
            case 'K':
                $num *= 1024;
                break;
            default:
                $num = (int)$str;
        }
        return $num;
    }
}