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

    /** @var ReadRequestHandler $readRequestHandler  */
    private $readRequestHandler;
    /** @var SendResponseHandler $sendResponseHandler */
    private $sendResponseHandler;
    /** @var ProcessRequestHandler $requestProcessHandler*/
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
		'wtrealm',   	//checking wtrealm instead of clientRequestId as it is optional
	);

	public function _handle_SSO()
	{
        $REQUESTS   = MoIDPUtility::sanitizeAssociateArray($_REQUEST);
		$keys 		= array_keys($REQUESTS);
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

    /**
     * @param $op
     * @throws InvalidServiceProviderException
     * @throws NotRegisteredException
     * @throws InvalidSSOUserException
     */
    public function _route_data($op)
    {
        $GETS 		= MoIDPUtility::sanitizeAssociateArray($_GET);
        $REQUESTS	= MoIDPUtility::sanitizeAssociateArray($_REQUEST);
        switch ($op)
        {
            case $this->requestParams[0]:
                $this->readRequestHandler->_read_request($REQUESTS,$GETS,MoIDPConstants::SAML);         break;
            case $this->requestParams[1]:
                $this->_initiate_saml_response($REQUESTS);                                              break;
            case $this->requestParams[2]:
                $this->readRequestHandler->_read_request($REQUESTS,$GETS,MoIDPConstants::WS_FED);       break;
        }
    }

	public function mo_idp_handle_post_login($login)
	{
        $COOKIES    = MoIDPUtility::sanitizeAssociateArray($_COOKIE);
		if(array_key_exists('response_params', $COOKIES) && !MoIDPUtility::isBlank($COOKIES['response_params']))
		{
			try{

				if(isset($COOKIES['moIdpsendSAMLResponse']) && strcmp( $COOKIES['moIdpsendSAMLResponse'], 'true') == 0)
					$this->sendResponseHandler->mo_idp_send_reponse ([
                            'requestType' => MoIDPConstants::AUTHN_REQUEST,
                            'acs_url' 	  => $COOKIES['acs_url'],
                            'issuer' 	  => $COOKIES['audience'],
                            'relayState'  => $COOKIES['relayState'],
                            'requestID'   => $COOKIES['requestID']
                    ], $login);

				if(isset($COOKIES['moIdpsendWsFedResponse']) && strcmp( $COOKIES['moIdpsendWsFedResponse'], 'true') == 0)
					$this->sendResponseHandler->mo_idp_send_reponse ([
                            'requestType' 		=> MoIDPConstants::WS_FED,
                            'clientRequestId' 	=> $COOKIES['clientRequestId'],
                            'wtrealm' 	  		=> $COOKIES['wtrealm'],
                            'wa'	 			=> $COOKIES['wa'],
                            'relayState' 		=> $COOKIES['relayState'],
                            'wctx' 				=> $COOKIES['wctx']
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

    /**
     * @param $REQUEST
     * @throws InvalidSSOUserException
     */
    private function _initiate_saml_response($REQUEST)
	{
		if ($REQUEST['option']=='testConfig')
			$this->sendSAMLResponseBasedOnRequestData($REQUEST);
		elseif ($REQUEST['option']==='saml_user_login')
			$this->sendSAMLResponseBasedOnSPName($REQUEST['sp'],$REQUEST['relayState']);
		elseif ($REQUEST['option']==='mo_idp_metadata')
			MoIDPUtility::showMetadata();
	}

    /**
     * @param $REQUEST
     * @throws InvalidSSOUserException
     */
    private function sendSAMLResponseBasedOnRequestData($REQUEST)
	{
		$defaultRelayState = !array_key_exists('defaultRelayState',$REQUEST)
								 || MoIDPUtility::isBlank($REQUEST['defaultRelayState']) ? '/' : $REQUEST['defaultRelayState'];
		$this->sendResponseHandler->mo_idp_send_reponse ([
            'requestType' => MoIDPConstants::AUTHN_REQUEST,
            'acs_url' 	  => $REQUEST['acs'],
            'issuer' 	  => $REQUEST['issuer'],
            'relayState'  => $defaultRelayState
        ]);
	}

    /**
     * @param $spName
     * @param $relayState
     * @throws InvalidSSOUserException
     */
    private function sendSAMLResponseBasedOnSPName($spName, $relayState)
	{
        /** @global \IDP\Helper\Database\MoDbQueries $dbIDPQueries */
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