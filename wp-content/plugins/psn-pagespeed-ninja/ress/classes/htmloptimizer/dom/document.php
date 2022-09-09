<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_HtmlOptimizer_Dom_Document extends DOMDocument
{
    /** @var string */
    protected $htmlPrefix = '';

    /**
     * @param string $version
     * @param string $encoding
     */
    public function __construct($version = null, $encoding = null)
    {
        parent::__construct($version, $encoding);
        $this->registerNodeClass('DOMElement', 'Ressio_HtmlOptimizer_Dom_Element');
        $this->registerNodeClass('DOMComment', 'Ressio_HtmlOptimizer_Dom_Comment');
        $this->registerNodeClass('DOMText', 'Ressio_HtmlOptimizer_Dom_Text');
        $this->registerNodeClass('DOMCdataSection', 'Ressio_HtmlOptimizer_Dom_CdataSection');
        $this->registerNodeClass('DOMAttr', 'Ressio_HtmlOptimizer_Dom_Attr');
    }

    /**
     * @param string $name
     * @param string $publicId
     * @param string $systemId
     */
    public function addDoctype($name, $publicId = '', $systemId = '')
    {
        if ($this->doctype) {
            $this->removeChild($this->doctype);
        }
        $this->htmlPrefix = '<!DOCTYPE ' . $name . ($publicId ? ' ' . $publicId : '') . ($systemId ? ' ' . $systemId : '') . ">\n";
    }

    /**
     * @param string|Ressio_HtmlOptimizer_Dom_Element $tag
     * @return Ressio_HtmlOptimizer_Dom_Element
     */
    public function addChild($tag)
    {
        $tag = is_object($tag) ? $this->importNode($tag, true) : $this->createElement($tag);
        $this->appendChild($tag);
        return $tag;
    }

    /**
     * @param string $source
     * @param int $options
     * @return bool
     */
    public function loadHTML($source, $options = 0)
    {
        // fix non-utf-8 characters
        $source = preg_replace('#(?<=[\x00-\x7F]|[\xC0-\xDF][\x80-\xBF]|[\xE0-\xEF][\x80-\xBF]{2}|[\xF0-\xF7][\x80-\xBF]{3})[\x80-\xBF]#S', "\xC0\\0", $source);

        // workaround for non-html4 tags
        if (preg_match('#<(?:!|command|keygen|source|track|wbr)#', $source)) {
            // @todo pre-extract <script> and <style> tags
            // fix html5 self closing tags
            $source = preg_replace('#<((command|keygen|source|track|wbr)\b[^>]*)/?>(?:</\2>)?#i', '<\1></\2>', $source);
            // keep IE's <![if ...]> and <![endif]> by converting to comments
            $source = preg_replace('#<(!\[(?:if\s.*?|endif)\])>#is', '<!--!RESS\1-->', $source);
        }

        $xml_errors = libxml_use_internal_errors(true);
        $xml_entityloader = libxml_disable_entity_loader();

        // $this->substituteEntities = true;
        // $source = mb_convert_encoding($source, 'HTML-ENTITIES', 'UTF-8');
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            if (stripos($source, '<!doctype') === false) {
                $source = "<!DOCTYPE html>\n" . $source;
            }
            $status = parent::loadHTML('<?xml encoding="utf-8" ?\>' . $source);
        } else {
            $status = parent::loadHTML('<?xml encoding="utf-8" ?\>' . $source, $options | LIBXML_HTML_NODEFDTD);
        }

        libxml_disable_entity_loader($xml_entityloader);
        libxml_use_internal_errors($xml_errors);

        foreach ($this->childNodes as $item) {
            if ($item->nodeType === XML_PI_NODE) {
                $this->removeChild($item);
                break;
            }
        }

        return $status;
    }

    /**
     * @param DOMNode $node
     * @return string
     */
    public function saveHTML($node = null)
    {
        if ($node !== null && version_compare(PHP_VERSION, '5.3.6', '<')) {
            $doc = new DOMDocument;
            $doc->appendChild($doc->importNode($node->cloneNode(true)));
            $html = $doc->saveHTML();
        } else {
            $html = parent::saveHTML($node);
        }
        if ($node === null) {
            $html = $this->htmlPrefix . $html;
        }
        $html = str_replace(array(
            '></command>',
            '></keygen>',
            '></source>',
            '></track>',
            '></wbr>',
        ), '>', $html);
        $html = preg_replace('#<!--!RESS(!.*?)-->#', '<\1>', $html);
        $html = rtrim($html);
        return $html;
    }
}
