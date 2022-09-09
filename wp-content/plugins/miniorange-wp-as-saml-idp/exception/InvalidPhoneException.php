<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class InvalidPhoneException extends \Exception
{
	public function __construct($phone) 
	{
		$message 	= MoIDPMessages::showMessage('ERROR_PHONE_FORMAT',array('phone'=>$phone));
		$code 		= 112;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}