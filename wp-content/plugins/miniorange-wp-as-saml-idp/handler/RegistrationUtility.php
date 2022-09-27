<?php

namespace IDP\Handler;

use IDP\Exception\InvalidPhoneException;
use IDP\Exception\OTPRequiredException;
use IDP\Exception\OTPSendingFailedException;
use IDP\Exception\OTPValidationFailedException;
use IDP\Exception\PasswordMismatchException;
use IDP\Exception\PasswordResetFailedException;
use IDP\Exception\PasswordStrengthException;
use IDP\Exception\RegistrationRequiredFieldsException;
use IDP\Exception\RequiredFieldsException;
use IDP\Helper\Utilities\MoIDPUtility;

class RegistrationUtility extends BaseHandler
{

    /**
     * @param $confirmPassword
     * @param $password
     * @throws PasswordStrengthException
     */
    public function checkPwdStrength($confirmPassword, $password)
	{
		if( strlen($password) < 6 || strlen($confirmPassword) < 6)
		    throw new PasswordStrengthException();
	}

    /**
     * @param $confirmPassword
     * @param $password
     * @throws PasswordMismatchException
     */
    public function pwdAndCnfrmPwdMatch($confirmPassword, $password)
	{
		if( $password != $confirmPassword )
		    throw new PasswordMismatchException();
	}

    /**
     * @param $array
     * @throws RegistrationRequiredFieldsException
     */
    public function checkIfRegReqFieldsEmpty($array)
	{
		try{
			$this->checkIfRequiredFieldsEmpty($array);
		}catch(RequiredFieldsException $e){
			throw new RegistrationRequiredFieldsException();
		}
	}

    /**
     * @param $phone
     * @throws InvalidPhoneException
     */
    public function isValidPhoneNumber($phone)
	{
		if(!MoIDPUtility::validatePhoneNumber($phone))
		    throw new InvalidPhoneException($phone);
	}

    /**
     * @param $array
     * @throws OTPRequiredException
     */
    public function checkIfOTPEntered($array)
	{
		try{
			$this->checkIfRequiredFieldsEmpty($array);
		}catch(RequiredFieldsException $e){
			throw new OTPRequiredException();
		}
	}

    /**
     * @param $array
     * @param $key
     * @throws OTPValidationFailedException
     */
    public function checkIfOTPValidationPassed($array, $key)
	{
		if(!array_key_exists($key,$array) || strcasecmp( $array[$key], 'SUCCESS' ) != 0)
		    throw new OTPValidationFailedException();
	}

    /**
     * @param $array
     * @param $key
     * @throws OTPSendingFailedException
     */
    public function checkIfOTPSentSuccessfully($array, $key)
	{
		if(!array_key_exists($key,$array) || strcasecmp( $array[$key], 'SUCCESS' ) != 0)
		    throw new OTPSendingFailedException();
	}

    /**
     * @param $array
     * @param $key
     * @throws PasswordResetFailedException
     */
    public function checkIfPasswordResetSuccesfully($array, $key)
	{
		if(!array_key_exists($key,$array) || strcasecmp( $array[$key], 'SUCCESS' ) != 0)
		    throw new PasswordResetFailedException();
	}
}