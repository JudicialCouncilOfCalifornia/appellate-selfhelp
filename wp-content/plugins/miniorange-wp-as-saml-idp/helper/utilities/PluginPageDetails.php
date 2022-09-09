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

    
    public $_pageTitle;

    
    public $_menuSlug;


    
    public $_menuTitle;


    
    public $_tabName;

    
    public $_description;
}