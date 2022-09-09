<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class InvalidSSOUserException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('INVALID_USER');
		$code 		= 121;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}