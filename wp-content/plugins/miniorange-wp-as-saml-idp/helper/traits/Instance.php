<?php

namespace IDP\Helper\Traits;

trait Instance {

    private static $_instance = null;

    /** @return self */
    public static function instance()
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

}