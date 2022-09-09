<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class OTPValidationFailedException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('INVALID_OTP');
		$code 		= 114;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}