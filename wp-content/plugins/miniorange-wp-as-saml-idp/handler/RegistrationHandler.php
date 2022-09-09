<?php

namespace IDP\Handler;

use IDP\Helper\Constants\MoIDPMessages;
use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\MoIDPUtility;
use IDP\Helper\Utilities\PluginPageDetails;
use IDP\Helper\Utilities\TabDetails;
use IDP\Helper\Utilities\Tabs;

final class RegistrationHandler extends RegistrationUtility
{
    use Instance;

    
    private function __construct()
    {
        $this->_nonce = 'reg_handler';
    }

    
    public function _idp_register_customer($POSTED)
	{
				$email 				= sanitize_email	 (	$POSTED['email'] 			);
		$password 			= sanitize_text_field( 	$POSTED['password'] 		);
		$confirmPassword 	= sanitize_text_field( 	$POSTED['confirmPassword'] 	);

        $this->checkIfRegReqFieldsEmpty(array($email,$password,$confirmPassword));
		$this->checkPwdStrength($password,$confirmPassword);
		$this->pwdAndCnfrmPwdMatch($password,$confirmPassword);

		update_site_option( 'mo_idp_admin_email'		, $email 		);
		update_site_option( 'mo_idp_admin_password'	, $password 	);

		$content = json_decode(MoIDPUtility::checkCustomer(), true);

		switch ($content['status'])
		{
			case 'CUSTOMER_NOT_FOUND':
                $this->_create_user_without_verification($email, $password);    break;
			default:
				$this->_get_current_customer($email,$password);		            break;
		}
	}

    
    public function _mo_idp_phone_verification($POSTED)
	{
		$phone 		= sanitize_text_field($POSTED['phone_number']);
		$phone 		= str_replace(' ', '', $phone);

		$this->isValidPhoneNumber($phone);
		update_site_option('mo_customer_validation_admin_phone',$phone);
		$this->_send_otp_token("",$phone,'SMS');
	}

	public function save_success_customer_config($id, $apiKey, $token, $appSecret)
	{
		update_site_option( 'mo_idp_admin_customer_key' , $id       );
		update_site_option( 'mo_idp_admin_api_key'      , $apiKey   );
		update_site_option( 'mo_idp_customer_token'     , $token    );
		delete_site_option( 'mo_idp_verify_customer'                );
		delete_site_option( 'mo_idp_new_registration'			   );
		delete_site_option( 'mo_idp_admin_password'				   );
		delete_site_option( 'mo_idp_registration_status'            );
	}

	public function _mo_idp_go_back()
	{
        $this->isValidRequest();

        delete_site_option('mo_idp_transactionId');
        delete_site_option('mo_idp_admin_password');
        delete_site_option('mo_idp_registration_status');
        delete_site_option('mo_idp_admin_phone');
        delete_site_option('mo_idp_new_registration');
        delete_site_option('mo_idp_admin_customer_key');
        delete_site_option('mo_idp_admin_api_key');
        delete_site_option('mo_idp_admin_email');
        if($_POST['option']==="remove_idp_account") {
            delete_site_option('sml_idp_lk');
            delete_site_option('t_site_status');
            delete_site_option('site_idp_ckl');
        }
        update_site_option('mo_idp_verify_customer', $_POST['option'] === "remove_idp_account");
        update_site_option("mo_idp_new_registration", $_POST['option']==="mo_idp_go_back");
        wp_redirect(getRegistrationURL());
	}

    
    public function _mo_idp_forgot_password()
	{
		$email 		= get_site_option('mo_idp_admin_email');
		$content 	= json_decode(MoIDPUtility::forgotPassword($email),true);
		$this->checkIfPasswordResetSuccesfully($content,'status');
		do_action('mo_idp_show_message',MoIDPMessages::showMessage('PASS_RESET'),'SUCCESS');
	}

    
    public function _mo_idp_verify_customer($POSTED)
	{
		$email 	  = sanitize_email( $POSTED['email'] );
		$password = sanitize_text_field( $POSTED['password'] );
		$this->checkIfRequiredFieldsEmpty(array($email,$password));
		$this->_get_current_customer($email,$password);
	}

    
    public function _send_otp_token($email, $phone, $auth_type)
	{
		$content  = json_decode(MoIDPUtility::sendOtpToken($auth_type,$email,$phone), true);
		$this->checkIfOTPSentSuccessfully($content,'status');

		update_site_option('mo_idp_transactionId', $content['txId']);
		update_site_option('mo_idp_registration_status', 'MO_OTP_DELIVERED_SUCCESS');
		if($auth_type=='EMAIL')
			do_action('mo_idp_show_message', MoIDPMessages::showMessage('EMAIL_OTP_SENT',array('email'=>$email)),'SUCCESS');
		else
			do_action('mo_idp_show_message', MoIDPMessages::showMessage('PHONE_OTP_SENT',array('phone'=>$phone)),'SUCCESS');
	}

	public function _get_current_customer($email,$password)
	{
		$content     = MoIdpUtility::getCustomerKey($email,$password);
		$customerKey = json_decode($content, true);
	
		if(json_last_error() == JSON_ERROR_NONE)
		{
			update_site_option( 'mo_idp_admin_email'		, $email 				 	);
						$this->save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
		}
		else
		{
			update_site_option('mo_idp_verify_customer'		, true		);
			delete_site_option('mo_idp_new_registration'					        );
			do_action('mo_idp_show_message',MoIDPMessages::showMessage('ACCOUNT_EXISTS'), 'ERROR');
		}
	}

    
    public function _idp_validate_otp($POSTED)
	{
		$otp_token 	= sanitize_text_field( $POSTED['otp_token'] );
		$this->checkIfOTPEntered(array('otp_token'=>$POSTED));

		$content = json_decode(MoIDPUtility::validateOtpToken(get_site_option('mo_idp_transactionId'), $otp_token ),true);
		$this->checkIfOTPValidationPassed($content,'status');
		$customerKey = json_decode( MoIDPUtility::createCustomer(), true );
		if( strcasecmp( $customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0 )
		{
            do_action('mo_idp_show_message',MoIDPMessages::showMessage('ACCOUNT_EXISTS'),'SUCCESS');
		}
		else if( strcasecmp( $customerKey['status'], 'SUCCESS' ) == 0 )
		{
			$this->save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
			do_action('mo_idp_show_message',MoIDPMessages::showMessage('NEW_REG_SUCCES'),'SUCCESS');
		}
	}

    
	public function _create_user_without_verification($email,$password)
    {
        $customerKey = json_decode( MoIDPUtility::createCustomer(), true );
        if( strcasecmp( $customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0 )
        {
            $this->_get_current_customer($email,$password);
        }
        else if( strcasecmp( $customerKey['status'], 'SUCCESS' ) == 0 )
        {
            $this->save_success_customer_config($customerKey['id'], $customerKey['apiKey'], $customerKey['token'], $customerKey['appSecret']);
            do_action('mo_idp_show_message',MoIDPMessages::showMessage('NEW_REG_SUCCES'),'SUCCESS');
        }
    }
}