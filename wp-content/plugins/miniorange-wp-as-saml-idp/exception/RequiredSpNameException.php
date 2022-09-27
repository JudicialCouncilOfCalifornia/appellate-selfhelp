<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class RequiredSpNameException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('SP_NAME_REQUIRED');
		$code 		= 130;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}