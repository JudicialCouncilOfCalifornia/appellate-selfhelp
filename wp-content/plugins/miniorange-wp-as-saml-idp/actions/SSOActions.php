<?php

namespace IDP\Actions;

use IDP\Exception\InvalidRequestInstantException;
use IDP\Exception\InvalidRequestVersionException;
use IDP\Exception\InvalidServiceProviderException;
use IDP\Exception\InvalidSignatureInRequestException;
use IDP\Exception\InvalidSSOUserException;
use IDP\Exception\NotRegisteredException;
use IDP\Handler\ProcessRequestHandler;
use IDP\Handler\ReadRequestHandler;
use IDP\Handler\SendResponseHandler;
use IDP\Helper\Constants\MoIDPConstants;
use IDP\Helper\Constants\MoIDPMessages;
use IDP\Helper\SAML2\AuthnRequest;
use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\MoIDPUtility;

class SSOActions
{
    use Instance;

    
    private $readRequestHandler;
    
    private $sendResponseHandler;
    
    private $requestProcessHandler;

    private function __construct()
    {
        $this->readRequestHandler = ReadRequestHandler::instance();
        $this->sendResponseHandler = SendResponseHandler::instance();
        $this->requestProcessHandler = ProcessRequestHandler::instance();

        add_action( 'init'					 , array( $this, '_handle_SSO' 					)		);
        add_action( 'wp_login'				 , array( $this, 'mo_idp_handle_post_login'		) , 99	);
    }

	private $requestParams = array (
		'SAMLRequest',
		'option',
		'wtrealm',   		);

	public function _handle_SSO()
	{
		$keys 		= array_keys($_REQUEST);
		$operation 	= array_intersect($keys,$this->requestParams);
		if(count($operation) <= 0) return;
		try{
			$this->_route_data(array_values($operation)[0]);
		}catch (NotRegisteredException $e) {
			if(MSI_DEBUG) MoIDPUtility::mo_debug("Exception Occurred during SSO " . $e);
			wp_die(MoIDPMessages::SAML_INVALID_OPERATION);
		}catch(InvalidRequestInstantException $e){
			if(MSI_DEBUG) MoIDPUtility::mo_debug("Exception Occurred during SSO " . $e);
			wp_die($e->getMessage());
		}catch(InvalidRequestVersionException $e){
			if(MSI_DEBUG) MoIDPUtility::mo_debug("Exception Occurred during SSO " . $e);
			wp_die($e->getMessage());
		}catch(InvalidServiceProviderException $e){
			if(MSI_DEBUG) MoIDPUtility::mo_debug("Exception Occurred during SSO " . $e);
			wp_die($e->getMessage());
		}catch(InvalidSignatureInRequestException $e){
			if(MSI_DEBUG) MoIDPUtility::mo_debug("Exception Occurred during SSO " . $e);
			wp_die($e->getMessage());
		}catch(InvalidSSOUserException $e){
			if(MSI_DEBUG) MoIDPUtility::mo_debug("Exception Occurred during SSO " . $e);
			wp_die($e->getMessage());
		}catch (\Exception $e){
			if(MSI_DEBUG) MoIDPUtility::mo_debug("Exception Occurred during SSO " . $e);
			wp_die($e->getMessage());
		}
	}

    
    public function _route_data($op)
	{
		switch ($op)
		{
			case $this->requestParams[0]:
				$this->readRequestHandler->_read_request($_REQUEST,$_GET,MoIDPConstants::SAML);		        break;
			case $this->requestParams[1]:
				$this->_initiate_saml_response($_REQUEST);								break;
			case $this->requestParams[2]:
				$this->readRequestHandler->_read_request($_REQUEST,$_GET,MoIDPConstants::WS_FED);		    break;
		}
	}

	public function mo_idp_handle_post_login($login)
	{
		if(array_key_exists('response_params', $_COOKIE) && !MoIDPUtility::isBlank($_COOKIE['response_params']))
		{
			try{

				if(isset($_COOKIE['moIdpsendSAMLResponse']) && strcmp( $_COOKIE['moIdpsendSAMLResponse'], 'true') == 0)
					$this->sendResponseHandler->mo_idp_send_reponse ([
                            'requestType' => MoIDPConstants::AUTHN_REQUEST,
                            'acs_url' 	  => $_COOKIE['acs_url'],
                            'issuer' 	  => $_COOKIE['audience'],
                            'relayState'  => $_COOKIE['relayState'],
                            'requestID'   => $_COOKIE['requestID']
                    ], $login);

				if(isset($_COOKIE['moIdpsendWsFedResponse']) && strcmp( $_COOKIE['moIdpsendWsFedResponse'], 'true') == 0)
					$this->sendResponseHandler->mo_idp_send_reponse ([
                            'requestType' 		=> MoIDPConstants::WS_FED,
                            'clientRequestId' 	=> $_COOKIE['clientRequestId'],
                            'wtrealm' 	  		=> $_COOKIE['wtrealm'],
                            'wa'	 			=> $_COOKIE['wa'],
                            'relayState' 		=> $_COOKIE['relayState'],
                            'wctx' 				=> $_COOKIE['wctx']
                    ], $login);

			}catch (NotRegisteredException $e) {
				if(MSI_DEBUG) MoIDPUtility::mo_debug("Exception Occurred during SSO " . $e);
				wp_die(MoIDPMessages::SAML_INVALID_OPERATION);
			}catch(InvalidSSOUserException $e){
				if(MSI_DEBUG) MoIDPUtility::mo_debug("Exception Occurred during SSO " . $e);
				wp_die($e->getMessage());
			}
		}
	}

    
    private function _initiate_saml_response($REQUEST)
	{
		if ($_REQUEST['option']=='testConfig')
			$this->sendSAMLResponseBasedOnRequestData($REQUEST);
		elseif ($_REQUEST['option']==='saml_user_login')
			$this->sendSAMLResponseBasedOnSPName($_REQUEST['sp'],$_REQUEST['relayState']);
	}

    
    private function sendSAMLResponseBasedOnRequestData($REQUEST)
	{
		$defaultRelayState = !array_key_exists('defaultRelayState',$REQUEST)
								 || MoIDPUtility::isBlank($_REQUEST['defaultRelayState']) ? '/' : $_REQUEST['defaultRelayState'];
		$this->sendResponseHandler->mo_idp_send_reponse ([
            'requestType' => MoIDPConstants::AUTHN_REQUEST,
            'acs_url' 	  => $_REQUEST['acs'],
            'issuer' 	  => $_REQUEST['issuer'],
            'relayState'  => $defaultRelayState
        ]);
	}

    
    private function sendSAMLResponseBasedOnSPName($spName, $relayState)
	{
        
		global $dbIDPQueries;
		$sp = $dbIDPQueries->get_sp_from_name($spName);
		if (!MoIDPUtility::isBlank($sp))
		{
			$defaultRelayState = !MoIDPUtility::isBlank($relayState) ? $relayState
								: ( MoIDPUtility::isBlank($sp->mo_idp_default_relayState) ? '/' : $sp->mo_idp_default_relayState );

            if(!is_user_logged_in()) {
                $requestObj = new AuthnRequest();
                $requestObj = $requestObj->setAssertionConsumerServiceURL($sp->mo_idp_acs_url)
                    ->setIssuer($sp->mo_idp_sp_issuer)
                    ->setRequestID(null);
                $this->requestProcessHandler->setSAMLSessionCookies( $requestObj, $defaultRelayState );
            }

			$this->sendResponseHandler->mo_idp_send_reponse ([
                'requestType' => MoIDPConstants::AUTHN_REQUEST,
                'acs_url' 	 => $sp->mo_idp_acs_url,
                'issuer' 	 => $sp->mo_idp_sp_issuer,
                'relayState' => $defaultRelayState
            ]);
		}
	}
}