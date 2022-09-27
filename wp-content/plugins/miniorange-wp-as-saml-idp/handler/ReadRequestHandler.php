<?php

namespace IDP\Handler;

use IDP\Exception\InvalidServiceProviderException;
use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\MoIDPUtility;
use IDP\Helper\SAML2\AuthnRequest;
use IDP\Helper\Factory\RequestDecisionHandler;
use IDP\Helper\WSFED\WsFedRequest;
use \RobRichards\XMLSecLibs\XMLSecurityKey;
use IDP\Helper\Utilities\SAMLUtilities;
use IDP\Helper\Constants\MoIDPConstants;

final class ReadRequestHandler extends BaseHandler
{
    use Instance;

    /** @var ProcessRequestHandler $requestProcessHandler*/
    private $requestProcessHandler;

    /** Private constructor to prevent direct object creation */
    private function __construct()
    {
        $this->requestProcessHandler = ProcessRequestHandler::instance();
    }

    /**
     * @param array $REQUEST
     * @param array $GET
     * @param $type
     * @throws \IDP\Exception\NotRegisteredException
     * @throws \IDP\Exception\InvalidServiceProviderException
     * @throws \IDP\Exception\InvalidSSOUserException
     */
    public function _read_request(array $REQUEST, array $GET, $type)
	{
		if(MSI_DEBUG) MoIDPUtility::mo_debug("Reading SAML Request");

		$this->checkIfValidPlugin();

		$requestObject 	= RequestDecisionHandler::getRequestHandler($type,$REQUEST,$GET);
		$relayState  	= array_key_exists('RelayState', $REQUEST) ? $REQUEST['RelayState'] : '/';

		if(MoIDPUtility::isBlank($requestObject)) return;

		switch($requestObject->getRequestType())
		{
			case MoIDPConstants::AUTHN_REQUEST:
				$this->mo_idp_process_assertion_request($requestObject, $relayState);   break;
			case MoIDPConstants::WS_FED:
				$this->mo_idp_process_ws_fed_request($requestObject, $relayState);      break;
		}
	}

    /**
     * @param WsFedRequest $wsFedRequestObject
     * @param $relayState
     * @throws \IDP\Exception\InvalidSSOUserException
     */
    public function mo_idp_process_ws_fed_request(WsFedRequest $wsFedRequestObject, $relayState)
	{
		if(MSI_DEBUG) MoIDPUtility::mo_debug($wsFedRequestObject); // display ws-fed request
		$this->checkIfValidPlugin();
		$this->requestProcessHandler->mo_idp_authorize_user($relayState,$wsFedRequestObject);
	}

    /**
     * @param AuthnRequest $authnRequest
     * @param $relayState
     * @throws \IDP\Exception\InvalidSSOUserException
     * @throws InvalidServiceProviderException
     */
    private function mo_idp_process_assertion_request(AuthnRequest $authnRequest, $relayState)
	{
        /** @global \IDP\Helper\Database\MoDbQueries $dbIDPQueries */
		global $dbIDPQueries;

		if(MSI_DEBUG) MoIDPUtility::mo_debug($authnRequest); //Display AuthnRequest Values

		$issuer = $authnRequest->getIssuer();
		$acs 	= $authnRequest->getAssertionConsumerServiceURL();

		$sp 	= $dbIDPQueries->get_sp_from_issuer($issuer);
		$sp 	= !isset($sp) ? $dbIDPQueries->get_sp_from_acs($acs) : $sp;

		$this->checkIfValidSP($sp);

		$issuer = $sp->mo_idp_sp_issuer;
		$acs 	= $sp->mo_idp_acs_url;

		$authnRequest->setIssuer($issuer);
		$authnRequest->setAssertionConsumerServiceURL($acs);

		$signatureData = SAMLUtilities::validateElement($authnRequest->getXml());
        $spCertificate = $sp->mo_idp_cert;
        $spCertificate = XMLSecurityKey::getRawThumbprint($spCertificate);
        $spCertificate = iconv("UTF-8", "CP1252//IGNORE", $spCertificate);
        $spCertificate = preg_replace('/\s+/', '', $spCertificate);

        if($signatureData !== FALSE) {
            $this->validateSignatureInRequest($spCertificate, $signatureData);
        }

        $relayState = MoIDPUtility::isBlank($sp->mo_idp_default_relayState) ? $relayState : $sp->mo_idp_default_relayState;

		$this->requestProcessHandler->mo_idp_authorize_user($relayState,$authnRequest);
	}

    /**
     * @param $sp
     * @throws InvalidServiceProviderException
     */
    public function checkIfValidSP($sp)
	{
		if(MoIDPUtility::isBlank($sp)) {
			throw new InvalidServiceProviderException();
		}
	}


    /*
     | -----------------------------------------------------------------------------------------------
     | FREE PLUGIN SPECIFIC FUNCTIONS
     | -----------------------------------------------------------------------------------------------
     */

    /**
     * This function checks the version of the SAML request made.
     * Makes sure the SAML request is a valid SAML 2.0 request.
     *
     * @param string $spCertificate refers to the certificate saved in SAML configuration
     * @param array  $signatureData refers to the Signature Data in the SAML request
     */
    public function validateSignatureInRequest($spCertificate, $signatureData)
    {
        return;
    }
}