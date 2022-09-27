<?php

namespace IDP\Exception;

use IDP\Helper\Constants\MoIDPMessages;

class MetadataFileException extends \Exception
{
	public function __construct() 
	{
		$message 	= MoIDPMessages::showMessage('METADATA_FILE_URL_NOT_UPLOADED');
		$code 		= 129;		
        parent::__construct($message, $code, NULL);
    }

    public function __toString() 
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}