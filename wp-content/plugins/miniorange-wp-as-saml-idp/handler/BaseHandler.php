<?php

namespace IDP\Handler;

use IDP\Exception\JSErrorException;
use IDP\Exception\RequiredFieldsException;
use IDP\Exception\SupportQueryRequiredFieldsException;
use IDP\Helper\Constants\MoIDPMessages;
use IDP\Helper\Utilities\MoIDPUtility;

class BaseHandler
{
    
    public $_nonce;

	public function checkIfValidPlugin()
	{
	                    		return TRUE;
	}

	public function isValidRequest()
    {
        if (!current_user_can('manage_options') || !check_admin_referer($this->_nonce)) {
            wp_die(MoIDPMessages::showMessage('INVALID_OP'));
        }
        return TRUE;
    }

    
    public function checkIfJSErrorMessage($array, $key='error_message')
	{
		if(array_key_exists($key,$array) && $array[$key]) throw new JSErrorException($array[$key]);
	}

    
    public function checkIfRequiredFieldsEmpty($array)
	{
		foreach ($array as $key => $value)
		{
			if(
				(is_array($value) && ( !array_key_exists($key,$value) || MoIDPUtility::isBlank($value[$key])) )
				|| MoIDPUtility::isBlank($value)
			  )
				throw new RequiredFieldsException();
		}
	}

    
    public function checkIfSupportQueryFieldsEmpty($array)
	{
		try{
			$this->checkIfRequiredFieldsEmpty($array);
		}catch(RequiredFieldsException $e){
			throw new SupportQueryRequiredFieldsException();
		}
	}
}