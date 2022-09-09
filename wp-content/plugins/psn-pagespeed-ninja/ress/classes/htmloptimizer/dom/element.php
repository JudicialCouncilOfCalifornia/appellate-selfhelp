<?php
/*
 * RESSIO Responsive Server Side Optimizer
 * https://github.com/ressio/
 *
 * @copyright   Copyright (C) 2013-2018 Kuneri, Ltd. All rights reserved.
 * @license     GNU General Public License version 2
 */

/**
 * @property-read DOMAttr[] $attributes
 */
class Ressio_HtmlOptimizer_Dom_Element extends DOMElement implements IRessio_HtmlNode
{
    /** @var DOMAttr[] $attributes */

    /**
     * @return string
     */
    public function getTag()
    {
        return $this->tagName;
    }

    public function addClass($class)
    {
        if (!$this->hasAttribute('class') || ($attr_class = $this->getAttribute('class')) === '') {
            $this->setAttribute('class', $class);
            return;
        }
        if (strpos(" $attr_class ", " $class ") === false) {
            $this->setAttribute('class', "$attr_class $class");
        }
    }

    public function removeClass($class)
    {
        if ($this->hasAttribute('class')) {
            $this->setAttribute('class', trim(str_replace(" $class ", ' ', ' ' . $this->getAttribute('class') . ' ')));
        }
    }
}
