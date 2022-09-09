<?php

namespace IDP\Handler;

use IDP\Helper\Constants\MoIDPMessages;
use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\MoIDPcURL;

final class SupportHandler extends BaseHandler
{
    use Instance;

    
    private function __construct(){}

    
    public function _mo_idp_support_query($POSTED)
	{
		$this->checkIfSupportQueryFieldsEmpty(array('mo_idp_contact_us_email'=>$POSTED,'mo_idp_contact_us_query'=>$POSTED));

		$email = sanitize_text_field($POSTED['mo_idp_contact_us_email']);
		$phone = sanitize_text_field($POSTED['mo_idp_contact_us_phone']);
		$query = sanitize_text_field($POSTED['mo_idp_contact_us_query']);

		$submited = MoIDPcURL::submit_contact_us( $email, $phone, $query );

		if ( $submited == FALSE )
			do_action('mo_idp_show_message',MoIDPMessages::showMessage('ERROR_QUERY'),'ERROR');
		else
			do_action('mo_idp_show_message',MoIDPMessages::showMessage('QUERY_SENT'),'SUCCESS');
	}
}