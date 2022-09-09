<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class RegistrationRequiredFieldsException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('REQUIRED_REGISTRATION_FIELDS');
		$code 		= 111;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}