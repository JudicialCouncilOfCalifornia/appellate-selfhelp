<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class MissingWaAttributeException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('MISSING_WA_ATTR');
		$code 		= 127;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}