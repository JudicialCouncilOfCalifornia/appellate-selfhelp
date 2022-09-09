<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class MissingNameIdException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('MISSING_NAMEID');
		$code 		= 126;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}