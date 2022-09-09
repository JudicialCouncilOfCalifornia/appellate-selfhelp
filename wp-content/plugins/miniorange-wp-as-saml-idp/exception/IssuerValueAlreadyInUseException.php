<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class IssuerValueAlreadyInUseException extends \Exception
{
	public function __construct($sp) 
	{
		$message 	= MoIDPMessages::showMessage('ISSUER_EXISTS',array('name'=>$sp->mo_idp_sp_name));
		$code 		= 106;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}