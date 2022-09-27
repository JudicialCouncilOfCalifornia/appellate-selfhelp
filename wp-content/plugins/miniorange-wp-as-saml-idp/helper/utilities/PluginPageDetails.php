<?php

namespace IDP\Helper\Utilities;

class PluginPageDetails
{
    function __construct($_pageTitle,$_menuSlug,$_menuTitle,$_tabName,$_description)
    {
        $this->_pageTitle =  $_pageTitle;
        $this->_menuSlug = $_menuSlug;
        $this->_menuTitle = $_menuTitle;
        $this->_tabName = $_tabName;
        $this->_description = $_description;
    }

    /**
     * The page title
     * @var string  $_pageTitle
     */
    public $_pageTitle;

    /**
     * The menuSlug
     * @var string  $_menuSlug
     */
    public $_menuSlug;


    /**
     * The menu title
     * @var string  $_menuTitle
     */
    public $_menuTitle;


    /**
     * Tab Name
     * @var String $_tabName
     */
    public $_tabName;

    /**
     * Tab Description
     * @var string $_description
     */
    public $_description;
}