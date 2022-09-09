<?php

namespace IDP\Handler;

use IDP\Helper\Constants\MoIDPConstants;
use IDP\Helper\SAML2\AuthnRequest;
use IDP\Helper\Traits\Instance;
use IDP\Helper\WSFED\WsFedRequest;

final class ProcessRequestHandler extends BaseHandler
{
    use Instance;

    
    private $sendResponseHandler;

    
    private function __construct()
    {
        $this->sendResponseHandler = SendResponseHandler::instance();
    }

    
    public function mo_idp_authorize_user($relayState, $requestObject)
	{
		switch($requestObject->getRequestType())
		{
			case MoIDPConstants::AUTHN_REQUEST:
				$this->startProcessForSamlResponse($relayState,$requestObject);         break;
			case MoIDPConstants::WS_FED:
				$this->startProcessForWsFedResponse($relayState,$requestObject);        break;
		}
	}

    
    public function startProcessForSamlResponse($relayState, $requestObject)
	{
		if(is_user_logged_in()) {
			$this->sendResponseHandler->mo_idp_send_reponse ( array(
                    'requestType' 	=> $requestObject->getRequestType(),
                    'acs_url' 		=> $requestObject->getAssertionConsumerServiceURL(),
                    'issuer' 		=> $requestObject->getIssuer(),
                    'relayState' 	=> $relayState,
                    'requestID' 	=> $requestObject->getRequestID()
                )
            );
		} else {
			$this->setSAMLSessionCookies($requestObject,$relayState);
		}
	}

    
    public function startProcessForWsFedResponse($relayState, $requestObject)
	{
		if(is_user_logged_in()) {
			$this->sendResponseHandler->mo_idp_send_reponse ( array (
                    'requestType' 		=> $requestObject->getRequestType(),
                    'clientRequestId' 	=> $requestObject->getClientRequestId(),
                    'wtrealm' 			=> $requestObject->getWtrealm(),
                    'wa' 				=> $requestObject->getWa(),
                    'relayState' 		=> $relayState,
                    'wctx' 				=> $requestObject->getWctx()
                )
            );
		} else {
			$this->setWSFedSessionCookies($requestObject,$relayState);
		}
	}

    
	public function setWSFedSessionCookies($requestObject,$relayState)
	{
        if (ob_get_contents()) ob_clean();
		setcookie("response_params","isSet");
		setcookie("moIdpsendWsFedResponse","true");
		setcookie("wtrealm",$requestObject->getWtrealm());
		setcookie("wa",$requestObject->getWa());
		setcookie("wctx",$requestObject->getWctx());
		setcookie("relayState",$relayState);
		setcookie("clientRequestId",$requestObject->getClientRequestId());
		wp_safe_redirect(wp_login_url());
		exit;
	}

    
	public function setSAMLSessionCookies($requestObject,$relayState)
	{
        if (ob_get_contents()) ob_clean();
		setcookie("response_params","isSet");
		setcookie("moIdpsendSAMLResponse","true");
		setcookie("acs_url",$requestObject->getAssertionConsumerServiceURL());
		setcookie("audience",$requestObject->getIssuer());
		setcookie("relayState",$relayState);
		setcookie("requestID",$requestObject->getRequestID());
		wp_safe_redirect(wp_login_url());
		exit;
	}
}