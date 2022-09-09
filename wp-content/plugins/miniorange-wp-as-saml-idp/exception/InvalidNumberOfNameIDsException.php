<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class InvalidNumberOfNameIDsException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('INVALID_NO_OF_NAMEIDS');
		$code 		= 124;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}