<?php

namespace IDP\Helper\Utilities;

use IDP\Helper\Traits\Instance;

class SupportedIntegrationsDetails {
    
    use Instance ;
    
    public $IntegrationDetails;

    private function __construct()
    {
        $this->IntegrationDetails = [
            'MemberPress' => new Integrations(
                MSI_URL.'/includes/images/memberpress.png',
                'MemberPress',  
            ),
        ];

    }


}