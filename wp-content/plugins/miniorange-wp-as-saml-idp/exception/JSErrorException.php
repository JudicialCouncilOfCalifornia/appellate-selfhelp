<?php

namespace IDP\Exception;

class JSErrorException extends \Exception
{
	public function __construct($message) 
	{
		$message 	= $message;
		$code 		= 103;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}