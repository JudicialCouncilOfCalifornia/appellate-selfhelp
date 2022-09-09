<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2019 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * Images scale down minification using GD
 */
class Ressio_ImgRescale_Gd implements IRessio_ImgRescale
{
    public $supported_exts = array('jpg', 'gif', 'png', 'wbmp');
    //public $thumbdir = 'imgcache';

    /** @var Ressio_DI */
    private $di;

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

        $src_imagesize = getimagesize($src_imagepath);
        if ($src_imagesize === false) {
            return $src_imagepath;
        }
        if (function_exists('memory_get_usage')) {
            $max_mem_size = ini_get('memory_limit');
            if ($max_mem_size < 0) {
                $max_mem_size = '2G';
            }
            $max_mem_size = $this->size2int($max_mem_size);
            $src_image_mem_size = 8300 + 6 * $src_imagesize[0] * $src_imagesize[1];
            $dest_image_mem_size = 8300 + 6 * $dest_width * $dest_height;
            $extra_mem_size = 16 * 1024 + 2 * filesize($src_imagepath); // variables and other things
            $required_mem_size = memory_get_usage() + $src_image_mem_size + $dest_image_mem_size + $extra_mem_size;
            if ($required_mem_size > $max_mem_size) {
                // @todo optimally allow to increase memory_limit up to a specified value
                // @todo (and restore memory_limit at the end of function)
                //ini_set('memory_limit', $required_mem_size);
                return $src_imagepath;
            }
        }

        // @todo add filelock?

        if (!$fs->copy($src_imagepath, $dest_imagepath)) {
            return $src_imagepath;
        }

        list($src_width, $src_height) = $src_imagesize;

        $src_image = false;
        switch ($src_ext) {
            case 'jpg':
                $src_image = imagecreatefromjpeg($dest_imagepath);
                break;
            case 'gif':
                $content = $fs->getContents($dest_imagepath);
                if ($this->is_gif_ani($content)) {
                    return $src_imagepath;
                }
                $src_image = imagecreatefromstring($content);
                unset($content);
                break;
            case 'wbmp':
                $src_image = imagecreatefromwbmp($dest_imagepath);
                break;
            case 'png':
                $src_image = imagecreatefrompng($dest_imagepath);
                if ($src_image === false) {
                    // try restore image
                    $content = $fs->getContents($dest_imagepath);
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
                $src_image = imagecreatefromwebp($dest_imagepath);
                break;
        }
        $fs->delete($dest_imagepath);

        if ($src_image === false) {
            return $src_imagepath;
        }

        $dest_image = imagecreatetruecolor($dest_width, $dest_height);

        //Additional operations to preserve transparency on images
        switch ($dest_ext) {
            case 'png':
            case 'gif':
            case 'webp':
                imagealphablending($dest_image, false);
                $color = imagecolortransparent($dest_image, imagecolorallocatealpha($dest_image, 0, 0, 0, 127));
                imagefilledrectangle($dest_image, 0, 0, $dest_width, $dest_height, $color);
                imagesavealpha($dest_image, true);
                break;
            default:
                $color = imagecolorallocate($dest_image, 255, 255, 255);
                imagefilledrectangle($dest_image, 0, 0, $dest_width, $dest_height, $color);
                break;
        }

        if (function_exists('imagecopyresampled')) {
            $ret = imagecopyresampled($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
        } else {
            $ret = imagecopyresized($dest_image, $src_image, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
        }
        if (!$ret) {
            imagedestroy($src_image);
            imagedestroy($dest_image);
            return $src_imagepath;
        }
        imagedestroy($src_image);

        $tmp_dir = sys_get_temp_dir();
        if ($this->open_basedir_enabled || !is_writable($tmp_dir)) {
            $tmp_dir = dirname($src_imagepath);
        }
        if (!is_writable($tmp_dir)) {
            return $src_imagepath;
        }
        $tmp_filename = tempnam($tmp_dir, 'Ressio');
        $data = false;
        switch ($dest_ext) {
            case 'jpg':
                imageinterlace($dest_image, true);
                // @todo check return values of image<format> functions
                imagejpeg($dest_image, $tmp_filename, $jpegquality);
                $data = file_get_contents($tmp_filename);
                $data = $this->jpeg_clean($data);
                break;
            case 'gif':
                imagetruecolortopalette($dest_image, true, 256);
                imagegif($dest_image, $tmp_filename);
                break;
            case 'wbmp':
                // Floyd-Steinberg dithering
                $black = imagecolorallocate($dest_image, 0, 0, 0);
                $white = imagecolorallocate($dest_image, 255, 255, 255);
                $next_err = array_fill(0, $dest_width, 0);
                for ($y = 0; $y < $dest_height; $y++) {
                    $cur_err = $next_err;
                    $next_err = array(-1 => 0, 0 => 0);
                    for ($x = 0, $err = 0; $x < $dest_width; $x++) {
                        $rgb = imagecolorat($dest_image, $x, $y);
                        $r = ($rgb >> 16) & 0xFF;
                        $g = ($rgb >> 8) & 0xFF;
                        $b = $rgb & 0xFF;
                        $color = $err + $cur_err[$x] + 0.299 * $r + 0.587 * $g + 0.114 * $b;
                        if ($color >= 128) {
                            imagesetpixel($dest_image, $x, $y, $white);
                            $err = $color - 255;
                        } else {
                            imagesetpixel($dest_image, $x, $y, $black);
                            $err = $color;
                        }
                        $next_err[$x - 1] += $err * 3 / 16;
                        $next_err[$x] += $err * 5 / 16;
                        $next_err[$x + 1] = $err / 16;
                        $err *= 7 / 16;
                    }
                }
                imagewbmp($dest_image, $tmp_filename);
                break;
            case 'png':
                if (version_compare(PHP_VERSION, '5.1.3', '>=')) {
                    imagepng($dest_image, $tmp_filename, 9, PNG_ALL_FILTERS);
                } elseif (version_compare(PHP_VERSION, '5.1.2', '>=')) {
                    imagepng($dest_image, $tmp_filename, 9);
                } else {
                    imagepng($dest_image, $tmp_filename);
                }
                break;
            case 'webp':
                imagewebp($dest_image, $tmp_filename, $jpegquality);
                break;
        }
        imagedestroy($dest_image);
        if ($data === false) {
            $data = file_get_contents($tmp_filename);
        }
        unlink($tmp_filename);

        // @todo check $data is not empty
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

    /**
     * Remove JFIF and Comment headers from GD2-generated jpeg (saves 79 bytes)
     * @param string $jpeg_src
     * @return bool|string
     */
    private function jpeg_clean($jpeg_src)
    {
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
                return $jpeg_clr . substr($jpeg_src, $pos);
            }
            $len = unpack('n', substr($jpeg_src, $pos + 2, 2));
            $len = array_shift($len);
            if ($b !== "\xE0" && $b !== "\xFE") {
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