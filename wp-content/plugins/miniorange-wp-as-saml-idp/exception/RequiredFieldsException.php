<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class RequiredFieldsException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('REQUIRED_FIELDS');
		$code 		= 104;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}