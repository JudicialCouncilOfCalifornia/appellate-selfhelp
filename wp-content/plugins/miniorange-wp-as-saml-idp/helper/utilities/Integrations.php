<?php

namespace IDP\Helper\Utilities;

use IDP\Helper\Traits\Instance;


class Integrations{

    function __construct($src_image , $title)
    {
        $this->title = $title ;
        $this->src_image = $src_image ;
    }

    public $src_image ;
    public $title ;

}
