<?php

namespace IDP\Handler;

use IDP\Helper\Constants\MoIDPConstants;
use IDP\Helper\Constants\MoIDPMessages;
use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\MoIDPcURL;

class DemoRequestHandler extends BaseHandler
{
	use Instance;

	/** Private constructor to avoid direct object creation */
	private function __construct()
	{
		$this->_nonce = 'mo_idp_demo_request';
	}

	public function mo_idp_demo_request_function($POSTED)
	{
		$this->isValidRequest();
		$this->checkIfSupportQueryFieldsEmpty( array(
			'mo_idp_demo_email'=>$POSTED,
			'mo_idp_demo_description'=>$POSTED
			));
		$email = sanitize_email( ($POSTED['mo_idp_demo_email']) );
		$query = sanitize_textarea_field( $POSTED['mo_idp_demo_description'] );
		
		$submited = $this->_sendEmail($this->_renderEmail($query, $email));

		if ( $submited == FALSE )
			do_action('mo_idp_show_message',MoIDPMessages::showMessage('ERROR_QUERY'),'ERROR');
		else
			do_action('mo_idp_show_message',MoIDPMessages::showMessage('QUERY_SENT'),'SUCCESS');
	}

    private function _renderEmail($message, $email)
    {
		$demoRequestTemplate = file_get_contents(MSI_DIR . 'includes/html/emailtemplate.min.html');
        //$email = get_site_option("mo_idp_admin_email");

        $demoRequestTemplate = str_replace("{{SERVER}}",esc_url_raw($_SERVER['SERVER_NAME']),$demoRequestTemplate);
        $demoRequestTemplate = str_replace("{{EMAIL}}",$email,$demoRequestTemplate);
        $demoRequestTemplate = str_replace("{{PLUGIN}}",MoIDPConstants::AREA_OF_INTEREST,$demoRequestTemplate);
        $demoRequestTemplate = str_replace("{{VERSION}}",MSI_VERSION,$demoRequestTemplate);
        $demoRequestTemplate = str_replace("{{TYPE}}","[Request a Demo]",$demoRequestTemplate);
        $demoRequestTemplate = str_replace("{{QUERY}}","Requirements : " . $message,$demoRequestTemplate);
		$demoRequestTemplate = str_replace("{{RATING}}","",$demoRequestTemplate);

        return $demoRequestTemplate;
    }

    private function _sendEmail($content)
    {
        $customerKey = get_site_option('mo_idp_admin_customer_key');
        $apiKey = get_site_option("mo_idp_admin_api_key");
        return MoIDPcURL::notify(
				!$customerKey ? MoIDPConstants::DEFAULT_CUSTOMER_KEY : $customerKey,
				!$apiKey ? MoIDPConstants::DEFAULT_API_KEY : $apiKey,
				MoIDPConstants::SAMLSUPPORT_EMAIL,
				$content,
				"Request a Demo : " . MoIDPConstants::AREA_OF_INTEREST
			);
    }
}


