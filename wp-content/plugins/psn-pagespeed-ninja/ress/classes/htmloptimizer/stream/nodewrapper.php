<?php

/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

class Ressio_HtmlOptimizer_Stream_NodeWrapper implements IRessio_HtmlNode
{
    /** @var string */
    public $tagName;
    /** @var string[] */
    public $attributes;
    /** @var string */
    public $prepend;
    /** @var string */
    public $tag;
    /** @var string */
    public $content;
    /** @var string */
    public $append;

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tagName;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return '<>';
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAttribute($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param string $name
     * @return string
     */
    public function getAttribute($name)
    {
        return $this->attributes[$name];
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * @param string $name
     */
    public function removeAttribute($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * @param string $class
     */
    public function addClass($class)
    {
        if (!isset($this->attributes['class']) || $this->attributes['class'] === '') {
            $this->attributes['class'] = $class;
            return;
        }
        if (strpos(' ' . $this->attributes['class'] . ' ', " $class ") === false) {
            $this->attributes['class'] .= " $class";
        }
    }

    /**
     * @param string $class
     */
    public function removeClass($class)
    {
        if (isset($this->attributes['class'])) {
            $this->attributes['class'] = trim(str_replace(" $class ", ' ', ' ' . $this->attributes['class'] . ' '));
        }
    }
}
