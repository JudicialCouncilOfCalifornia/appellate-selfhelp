<?php

namespace IDP\Handler;


use IDP\Helper\Constants\MoIDPConstants;
use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\MoIDPcURL;
use IDP\Helper\Utilities\MoIDPUtility;

final class FeedbackHandler extends BaseHandler
{
    use Instance;

    
    private function __construct()
    {
        $this->_nonce = 'mo_idp_feedback';
    }

    public function _mo_send_feedback()
    {
        $this->isValidRequest();
        $submitType = $_POST['miniorange_feedback_submit'];
        $feedback = sanitize_textarea_field($_POST['query_feedback']);

        if($submitType!=="Skip & Deactivate"){
            $this->_sendEmail($this->_renderEmail($feedback));         }

        deactivate_plugins([MSI_PLUGIN_NAME]);
    }

    
    private function _renderEmail($message)
    {
        $feedbackTemplate = file_get_contents(MSI_DIR . 'includes/html/feedback.min.html');
        $email = get_site_option("mo_idp_admin_email");

        $feedbackTemplate = str_replace("{{SERVER}}",$_SERVER['SERVER_NAME'],$feedbackTemplate);
        $feedbackTemplate = str_replace("{{EMAIL}}",$email,$feedbackTemplate);
        $feedbackTemplate = str_replace("{{PLUGIN}}",MoIDPConstants::AREA_OF_INTEREST,$feedbackTemplate);
        $feedbackTemplate = str_replace("{{VERSION}}",MSI_VERSION,$feedbackTemplate);
        $feedbackTemplate = str_replace("{{TYPE}}","[Plugin Deactivated]",$feedbackTemplate);
        $feedbackTemplate = str_replace("{{FEEDBACK}}",$message,$feedbackTemplate);
        return $feedbackTemplate;
    }


    
    private function _sendEmail($content)
    {
        $customerKey = get_site_option('mo_idp_admin_customer_key');
        $apiKey = get_site_option("mo_idp_admin_api_key");
        MoIDPcURL::notify(
            !$customerKey ? MoIDPConstants::DEFAULT_CUSTOMER_KEY : $customerKey,
            !$apiKey ? MoIDPConstants::DEFAULT_API_KEY : $apiKey,
            MoIDPConstants::FEEDBACK_EMAIL,
            $content,
            "WordPress IDP Plugin Deactivated"
        );
    }
}