<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class OTPSendingFailedException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('ERROR_SENDING_OTP');
		$code 		= 115;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}