<?php

class Ressio_Plugin_WpLazyload extends Ressio_Plugin_Lazyload
{
    /**
     * @param $src_imagepath string
     * @param $dest_width int
     * @param $dest_height int
     * @param $dest_ext string
     * @return string
     */
    public function getRescaledPath($src_imagepath, $dest_width, $dest_height, $dest_ext)
    {
        if (defined('PATHINFO_FILENAME')) {
            $src_imagename = pathinfo($src_imagepath, PATHINFO_FILENAME);
        } else {
            $base = basename($src_imagepath);
            $src_imagename = substr($base, 0, strrpos($base, '.'));
        }

        return dirname($src_imagepath) . '/' . $src_imagename . '-' . $dest_width . 'x' . $dest_height . '.' . $dest_ext;
    }
}