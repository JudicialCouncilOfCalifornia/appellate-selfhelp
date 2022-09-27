<?php

namespace IDP\Handler;

use IDP\Helper\Constants\MoIDPMessages;
use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\MoIDPcURL;

final class SupportHandler extends BaseHandler
{
    use Instance;

    /** Private constructor to avoid direct object creation */
    private function __construct(){}

    /**
     * @param $POSTED
     * @throws \IDP\Exception\SupportQueryRequiredFieldsException
     */
    public function _mo_idp_support_query($POSTED)
	{
		$this->checkIfSupportQueryFieldsEmpty( array(
			'mo_idp_contact_us_email'=>$POSTED,
			'mo_idp_contact_us_query'=>$POSTED
		));

		$email = sanitize_email($POSTED['mo_idp_contact_us_email']);
		$phone = sanitize_text_field($POSTED['mo_idp_contact_us_phone']);
		$query = sanitize_textarea_field($POSTED['mo_idp_contact_us_query']);

		if(array_key_exists('mo_idp_upgrade_plan_name',$POSTED))
		{
			$plan_name 	= sanitize_text_field($POSTED['mo_idp_upgrade_plan_name']);
			$plan_users = sanitize_text_field($POSTED['mo_idp_upgrade_plan_users']);
			$query = "Plan Name : ".$plan_name.", Users : ".$plan_users.", ".$query;
		}

		$submited = MoIDPcURL::submit_contact_us( $email, $phone, $query );

		if ( $submited == FALSE )
			do_action('mo_idp_show_message',MoIDPMessages::showMessage('ERROR_QUERY'),'ERROR');
		else
			do_action('mo_idp_show_message',MoIDPMessages::showMessage('QUERY_SENT'),'SUCCESS');
	}
}