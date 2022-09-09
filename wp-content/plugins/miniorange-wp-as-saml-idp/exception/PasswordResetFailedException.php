<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class PasswordResetFailedException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('ERROR_OCCURRED');
		$code 		= 116;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}