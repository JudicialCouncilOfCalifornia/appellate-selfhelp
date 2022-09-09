<?php 

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class NotRegisteredException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('NOT_REG_ERROR');
		$code 		= 102;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}