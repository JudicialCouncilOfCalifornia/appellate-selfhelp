<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class NoServiceProviderConfiguredException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('NO_SP_CONFIG');
		$code 		= 101;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}