<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class SPNameAlreadyInUseException extends \Exception
{
	public function __construct($sp) 
	{
		$message 	= MoIDPMessages::showMessage('SP_EXISTS');
		$code 		= 107;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}