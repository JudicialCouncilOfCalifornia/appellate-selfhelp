<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

defined('RESSIO_PATH') or die('RESS: Restricted access');

require_once RESSIO_LIBS . '/csstidy/class.csstidy.php';

/**
 * CSS minification using CSS Tidy
 */
class Ressio_CssMinify_CssTidy extends Ressio_CssMinify_None
{
    /**
     * Minify CSS
     * @param string $str
     * @param string $srcBase
     * @param string $targetBase
     * @return string
     * @throws ERessio_InvalidCss
     */
    public function minify($str, $srcBase = null, $targetBase = null)
    {
        $str = parent::minify($str, $srcBase, $targetBase);

        $css = $this->getCSSTidy();
        if (!$css->parse($str)) {
            throw new ERessio_InvalidCss('Invalid CSS');
        }
        $str = $css->print->plain();

        return $str;
    }

    /**
     * Minify CSS in style=""
     * @param string $str
     * @param string $srcBase
     * @return string
     */
    public function minifyInline($str, $srcBase = null)
    {
        $str = '*{' . $str . '}';

        $css = $this->getCSSTidy();
        $css->parse($str);
        $str = $css->print->plain();

        $str = trim($str, '* {}');

        return $str;
    }

    /**
     * Get CSSTidy object
     * @return csstidy
     */
    private function getCSSTidy()
    {
        static $obj;
        if ($obj === null) {
            $obj = new csstidy;
            $obj->load_template('highest_compression');
            $obj->set_cfg('remove_last_;', true);
        }
        return $obj;
    }
}