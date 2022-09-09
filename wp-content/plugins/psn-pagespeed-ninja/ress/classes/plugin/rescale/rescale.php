<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_Plugin_Rescale extends Ressio_Plugin
{
    /**
     * @param Ressio_DI $di
     * @param null|stdClass $params
     * @throws ERessio_UnknownDiKey
     */
    public function __construct($di, $params = null)
    {
        $params = $this->loadConfig(dirname(__FILE__) . '/config.json', $params);

        parent::__construct($di, $params);
    }

    /**
     * @param $event Ressio_Event
     * @param $optimizer IRessio_HtmlOptimizer
     * @param $node IRessio_HtmlNode
     * @throws ERessio_UnknownDiKey
     */
    public function onHtmlIterateTagIMG($event, $optimizer, $node)
    {
        if ($optimizer->nodeIsDetached($node)) {
            return;
        }

        // @todo: parse srcset attribute
        if ($node->hasAttribute('src')) {
            if ($this->params->scaletype === 'remove') {
                $optimizer->nodeDetach($node);
            } else {
                // @todo move all urlRewriter-related code here
                $this->imageRescale($node, $optimizer);
            }
        }
    }

    /**
     * @param $node IRessio_HtmlNode
     * @param $optimizer IRessio_HtmlOptimizer
     * @throws ERessio_UnknownDiKey
     */
    private function imageRescale(&$node, $optimizer)
    {
        // @todo: rescaled img width should be multiple of 16
        //       (to avoid possible incorrect sizes in device database)

        $rescaler = $this->di->imgRescaler;
        $device = $this->di->deviceDetector;

        if ($rescaler && $device) {
            $forced_width = 0;
            $forced_height = 0;
            if ($node->hasAttribute('style')) {
                $style = $node->getAttribute('style');
                if (preg_match('#(?:^|\s|[;{])width\s*:\s*(\d+)px#', $style, $matches)) {
                    $forced_width = (int)$matches[1];
                }
                if (preg_match('#(?:^|\s|[;{])height\s*:\s*(\d+)px#', $style, $matches)) {
                    $forced_height = (int)$matches[1];
                }
            }
            if ($node->hasAttribute('width')) {
                $forced_width = (int)$node->getAttribute('width');
            }
            if ($node->hasAttribute('height')) {
                $forced_height = (int)$node->getAttribute('height');
            }

            $scaledimage_width = $forced_width;
            $scaledimage_height = $forced_height;

            $src_url = $node->getAttribute('src');
            $imageurl = $src_url;

            $src_ext = strtolower(pathinfo($src_url, PATHINFO_EXTENSION));
            if ($src_ext === 'jpeg') {
                $src_ext = 'jpg';
            }

            $dest_imageuri = $src_url;

            if (in_array($src_ext, $rescaler->getSupportedExts(), true)) {
                $urlRewriter = $this->di->urlRewriter;

                $src_imagepath = $urlRewriter->urlToFilepath($imageurl);
                if ($this->di->filesystem->isFile($src_imagepath)) {
                    $src_imagesize = getimagesize($src_imagepath);
                    if ($src_imagesize !== false) {
                        list($src_width, $src_height) = $src_imagesize;
                        if ($src_width > 0 && $src_height > 0) {
                            //$dev_width = $device->screen_width();
                            //$dev_height = $device->_screen_height();
                            // optimize for both portrait & landscape orientation [@todo move to options]
                            $dev_width = $dev_height = max($device->screen_width(), $device->screen_height());

                            $formats = $device->browser_imgformats();
                            if (is_array($formats) && count($formats) > 0 && !empty($formats[0])) {
                                $templateBuffer = $this->params->bufferwidth;
                                if ($node->hasAttribute('ress-fullwidth')) {
                                    $templateBuffer = 0;
                                    $node->removeAttribute('ress-fullwidth');
                                }
                                $dev_width -= $templateBuffer;
                                if ($dev_width < 16) {
                                    $dev_width = 16;
                                }

                                if ($forced_width === 0) {
                                    if ($forced_height === 0) {
                                        $forced_width = $src_width;
                                        $forced_height = $src_height;
                                    } else {
                                        $forced_width = round($src_width * $forced_height / $src_height);
                                        if ($forced_width === 0) {
                                            $forced_width = 1;
                                        }
                                    }
                                } elseif ($forced_height === 0) {
                                    $forced_height = round($src_height * $forced_width / $src_width);
                                    if ($forced_height === 0) {
                                        $forced_height = 1;
                                    }
                                }

                                if ($this->params->scaletype === 'prop') {
                                    $scalewidth = $this->params->templatewidth;
                                    $defscale = $dev_width / $scalewidth;
                                } else {
                                    $defscale = 1;
                                }

                                $maxscalex = $dev_width / $forced_width;
                                $maxscaley = $dev_height / $forced_height;
                                $scale = min($defscale, $maxscalex, $maxscaley);
                                $allowedFormat = in_array($src_ext, $formats, true);
                                if ($scale >= 1 && $allowedFormat &&
                                    $forced_width === $src_width && $forced_height === $src_height
                                ) {
                                    $scaledimage_width = $src_width;
                                    $scaledimage_height = $src_height;
                                } else {
                                    $dest_width = $scaledimage_width = round($forced_width * $scale);
                                    $dest_height = $scaledimage_height = round($forced_height * $scale);
                                    if ($dest_width === 0) {
                                        $dest_width = 1;
                                    }
                                    if ($dest_height === 0) {
                                        $dest_height = 1;
                                    }

                                    $dpr = $device->screen_dpr();
                                    if ($this->params->hiresimages && $dpr > 1) {
                                        $dest_width *= $dpr;
                                        $dest_height *= $dpr;
                                        $this->config->img->jpegquality = $this->params->hiresjpegquality;
                                    }

                                    if ($allowedFormat) {
                                        $dest_ext = $src_ext;
                                    } else {
                                        $dest_ext = $formats[0];
                                    }

                                    $dest_imagepath = $this->getRescaledPath($src_imagepath, $dest_width, $dest_height, $dest_ext);

                                    $dest_imagepath = $rescaler->rescale($src_imagepath, $dest_imagepath, $dest_width, $dest_height, $dest_ext);

                                    $dest_imageuri = $urlRewriter->filepathToUrl($dest_imagepath);
                                }
                            }
                        }
                    }
                }
            }

            if ($this->params->setdimension && $scaledimage_width && $scaledimage_height) {
                $node->setAttribute('width', $scaledimage_width);
                $node->setAttribute('height', $scaledimage_height);
            }

            if ($src_url !== $dest_imageuri) {
                if ($this->params->keeporig) {
                    $node->setAttribute('data-orig', $src_url);
                }
                $node->setAttribute('src', $dest_imageuri);
            }

            if ($this->params->wrapwideimg && !$node->hasAttribute('ress-nowrap')) {
                $screenWidth = $device->screen_width();
                if ($screenWidth > 0 && $scaledimage_width > $screenWidth / 2) {
                    $optimizer->nodeWrap($node, 'span', array('class' => $this->params->wideimgclass));
                }
            } else {
                $node->removeAttribute('ress-nowrap');
            }
        }
    }

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

        // @todo: move cache dir to settings
        $dest_imagedir = dirname($src_imagepath) . '/imgcache';

        return $dest_imagedir . '/' . $src_imagename . '_' . $dest_width . 'x' . $dest_height . '.' . $dest_ext;
    }
}