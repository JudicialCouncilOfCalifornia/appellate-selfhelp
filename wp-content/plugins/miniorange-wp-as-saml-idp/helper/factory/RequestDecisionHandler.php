<?php 

namespace IDP\Helper\Factory;

use IDP\Helper\Constants\MoIDPConstants;
use IDP\Helper\SAML2\AuthnRequest;
use IDP\Helper\WSFED\WsFedRequest;

class RequestDecisionHandler
{

	public static function getRequestHandler($type,$REQUEST,$GET,$args=array())
	{
		switch ($type) 
		{
			case MoIDPConstants::SAML:
				return self::getSAMLRequestHandler($REQUEST,$GET);  						break;
			case MoIDPConstants::WS_FED:
				return self::getWSFedRequestHandler($REQUEST,$GET); 						break;
			case MoIDPConstants::AUTHN_REQUEST:
				return new AuthnRequest($args[0]);											break;
		}
	}

	public static function getSAMLRequestHandler($REQUEST,$GET)
	{
		$samlRequest = $REQUEST['SAMLRequest'];
		$samlRequest = base64_decode($samlRequest);
		if(array_key_exists('SAMLRequest', $GET)) {
			$samlRequest = gzinflate($samlRequest);
		}

		$document = new \DOMDocument();
		$document->loadXML($samlRequest);
		$samlRequestXML = $document->firstChild;
		if( $samlRequestXML->localName == 'LogoutRequest' )
			return;
		else
			return new AuthnRequest($samlRequestXML);
	}

	public static function getWSFedRequestHandler($REQUEST,$GET)
	{
		return new WsFedRequest($REQUEST);
	}

	public static function getAuthnRequestHandler($xml)
	{
				return;
	}
}