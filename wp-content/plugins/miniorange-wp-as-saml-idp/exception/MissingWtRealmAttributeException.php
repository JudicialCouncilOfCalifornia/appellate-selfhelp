<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class MissingWtRealmAttributeException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('MISSING_WTREALM_ATTR');
		$code 		= 128;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}