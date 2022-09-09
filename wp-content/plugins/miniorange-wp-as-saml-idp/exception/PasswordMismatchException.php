<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class PasswordMismatchException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('PASS_MISMATCH');
		$code 		= 122;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}