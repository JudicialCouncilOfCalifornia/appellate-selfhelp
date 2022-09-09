<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class PasswordStrengthException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('INVALID_PASS_STRENGTH');
		$code 		= 110;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}