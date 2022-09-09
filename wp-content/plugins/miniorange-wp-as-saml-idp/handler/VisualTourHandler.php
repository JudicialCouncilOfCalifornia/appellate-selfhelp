<?php

namespace IDP\Handler;

use IDP\Helper\Traits\Instance;
use IDP\VisualTour\PointersManager;

final class VisualTourHandler extends BaseHandler
{
    use Instance;

    
    private function __construct(){}

    
    public function _mo_restart_tour($POSTED)
    {
        
        $pointersManager = PointersManager::instance();
        $pointersManager->clear();
        return;
    }
}