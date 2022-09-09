<?php

namespace IDP\Helper\WSFED;

use IDP\Helper\Utilities\MoIDPUtility;
use IDP\Helper\Utilities\SAMLUtilities;
use \RobRichards\XMLSecLibs\XMLSecurityKey;
use \RobRichards\XMLSecLibs\XMLSecurityDSig;
use \RobRichards\XMLSecLibs\XMLSecEnc;
use IDP\Exception\InvalidSSOUserException;
use IDP\Helper\Factory\ResponseHandlerFactory;

class GenerateWsFedResponse implements ResponseHandlerFactory
{
	private $xml;
	private $issuer;
	private $wtrealm;
	private $wa;
	private $wctx;
	private $subject;
	private $mo_idp_nameid_attr;
	private $mo_idp_nameid_format;
	private $currentUser;

	function __construct($wtrealm, $wa, $wctx, $issuer, $sp, $sp_attr, $login)
	{
		$this->xml = new \DOMDocument("1.0", "utf-8");
		$this->xml->preserveWhiteSpace = false;
		$this->xml->formatOutput = false;
		$this->wctx = $wctx;
		$this->issuer = $issuer;
		$this->wtrealm = $wtrealm;
		$this->sp_attr = $sp_attr;
		$this->wa = $wa;
		$this->mo_idp_nameid_format = $sp->mo_idp_nameid_format;
		$this->mo_idp_nameid_attr = $sp->mo_idp_nameid_attr;
		$this->current_user = is_null($login) ? wp_get_current_user() : get_user_by('login',$login);
	}

	function generateResponse()
	{
		if(MoIDPUtility::isBlank($this->current_user)) throw new InvalidSSOUserException();
		$response_params = $this->getResponseParams();

				$resp = $this->createResponseElement($response_params);
		$this->xml->appendChild($resp);

				$private_key = MoIDPUtility::getPrivateKey();
		$this->signNode($private_key, $resp->firstChild->nextSibling->nextSibling->firstChild, NULL, $response_params);

		$xmlResponseString = $this->xml->saveXML();
		return $xmlResponseString;
	}

	function getResponseParams()
	{
		$response_params = array();
		$time = time();
		$response_params['IssueInstant'] = str_replace('+00:00','Z',gmdate("c",$time));
		$response_params['NotOnOrAfter'] = str_replace('+00:00','Z',gmdate("c",$time+300));
		$response_params['NotBefore'] = str_replace('+00:00','Z',gmdate("c",$time-30));
		$response_params['AuthnInstant'] = str_replace('+00:00','Z',gmdate("c",$time-120));
		$response_params['AssertID'] = $this->generateUniqueID(40);

		$public_key	= MoIDPUtility::getPublicCert();
		$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256,array( 'type' => 'public'));
		$objKey->loadKey($public_key, FALSE,TRUE);
		$response_params['x509'] = $objKey->getX509Certificate();
		return $response_params;
	}

	function generateUniqueID($length)
	{
		return MoIDPUtility::generateRandomAlphanumericValue($length);
	}

	function createResponseElement($response_params)
	{

		$resp = $this->xml->createElementNS('http://schemas.xmlsoap.org/ws/2005/02/trust','t:RequestSecurityTokenResponse');

		$resp1 = $this->createResponseElementLifetime($response_params);
		$resp->appendChild($resp1);

		$resp2 = $this->createResponseElementAppliesTo($response_params);
		$resp->appendChild($resp2);

		$resp3 = $this->create_RequestedSecurityToken($response_params);

		$resp->appendChild($resp3);

		$resp4 = $this->create_TokenType();
		$resp->appendChild($resp4);

		$resp5 = $this->create_RequestType();
		$resp->appendChild($resp5);

		$resp6 = $this->create_KeyType();
		$resp->appendChild($resp6);

		return $resp;
	}

	function create_RequestType()
	{
		$resp = $this->xml->createElement('t:TokenType','urn:oasis:names:tc:SAML:1.0:assertion');
		return $resp;
	}

	function create_KeyType()
	{
		$resp = $this->xml->createElement('t:KeyType','http://schemas.xmlsoap.org/ws/2005/05/identity/NoProofKey');
		return $resp;
	}

	function create_TokenType()
	{
		$resp = $this->xml->createElement('t:RequestType','http://schemas.xmlsoap.org/ws/2005/02/trust/Issue');
		return $resp;
	}

	function create_RequestedSecurityToken($response_params)
	{
		$resp=$this->xml->createElement('t:RequestedSecurityToken');
		$resp1=$this->create_Assertion($response_params);
		$resp->appendChild($resp1);
		return $resp;
	}

	function create_Assertion($response_params)
	{
		$assertion = $this->xml->createElementNS('urn:oasis:names:tc:SAML:1.0:assertion','saml:Assertion');
		$assertion->setAttribute('MajorVersion','1');
		$assertion->setAttribute('MinorVersion','1');
		$assertion->setAttribute('AssertionID',$response_params['AssertID']);
		$assertion->setAttribute('Issuer',$this->issuer);
		$assertion->setAttribute('IssueInstant',$response_params['IssueInstant']);

		$samlConditions =$this->createSamlConditions($response_params);
		$assertion->appendChild($samlConditions);

		$authnStatement =$this->createAuthenticationStatement($response_params);
		$assertion->appendChild($authnStatement);

		return $assertion;
	}

	function signNode($private_key, $node, $subject,$response_params)
	{
		$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256,array( 'type' => 'private'));
		$objKey->loadKey($private_key, FALSE);

				$objXMLSecDSig = new XMLSecurityDSig();
		$objXMLSecDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);

		$objXMLSecDSig->addReferenceList(array($node), XMLSecurityDSig::SHA256
										,array('http://www.w3.org/2000/09/xmldsig#enveloped-signature'
										, XMLSecurityDSig::EXC_C14N)
										,array('id_name'=>'AssertionID','overwrite'=>false));
		$objXMLSecDSig->sign($objKey);
		$objXMLSecDSig->add509Cert($response_params['x509']);

		$objXMLSecDSig->insertSignature($node, NULL);
	}

	function creatSignedInfo()
	{
		$resp=$this->xml->createElement('ds:Signed');
		$resp1=$this->createCanonicalizationMethod();
		$resp->appendChild($resp1);
		return $resp;
	}

	function createCanonicalizationMethod()
	{
		$resp=$this->xml->createElement('ds:CanonicalizationMethod');
		$resp->setAttribute('Algorithm','http://www.w3.org/2001/10/xml-exc-c14n#');
		return $resp;
	}

	function createAuthenticationStatement($response_params)
	{
		$resp = $this->xml->createElement('saml:AuthenticationStatement');
		$resp->setAttribute('AuthenticationMethod','urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport');
		$resp->setAttribute('AuthenticationInstant',$response_params['AuthnInstant']);
		$resp1 = $this->createSubject();
		$this->subject = $resp1;
		$resp->appendChild($resp1);

		return $resp;
	}

	function createSubjectConfirmation()
	{
		$resp = $this->xml->createElement('saml:SubjectConfirmation');
		$resp1 = $this->createConfirmationMethod();
		$resp->appendChild($resp1);
		return $resp;
	}

	function createConfirmationMethod()
	{
		$resp = $this->xml->createElement('saml:ConfirmationMethod','urn:oasis:names:tc:SAML:1.0:cm:bearer');
		return $resp;
	}

	function createSubject()
	{
		$resp = $this->xml->createElement('saml:Subject');
		$resp1 = $this->createNameId();
		$resp->appendChild($resp1);
		$resp2 = $this->createSubjectConfirmation();
		$resp->appendChild($resp2);
		return $resp;
	}

	function createNameId()
	{
		$nameID = !empty($this->mo_idp_nameid_attr) && $this->mo_idp_nameid_attr!='emailAddress' ? $this->mo_idp_nameid_attr : "user_email";
		$value = MoIDPUtility::isBlank($this->current_user->$nameID)
					? get_user_meta($this->current_user->ID, $nameID, true) : $this->current_user->$nameID;

		$value = apply_filters('generate_wsfed_attribute_value',$value,$this->current_user,'NameID');
		$resp=$this->xml->createElement('saml:NameIdentifier',$value);
		$resp->setAttribute('Format','urn:oasis:names:tc:SAML:'.$this->mo_idp_nameid_format);
		return 	$resp;
	}

	function createSamlConditions($response_params)
	{
		$resp=$this->xml->createElement('saml:Conditions');
		$resp->setAttribute('NotBefore',$response_params['NotBefore']);
		$resp->setAttribute('NotOnOrAfter',$response_params['NotOnOrAfter']);

		$resp1=$this->createSamlAudience();
		$resp->appendChild($resp1);
		return $resp;
	}

	function createSamlAudience()
	{
		$resp=$this->xml->createElement('saml:AudienceRestrictionCondition');
		$resp1=$this->buildAudience();
		$resp->appendChild($resp1);
		return $resp;
	}

	function buildAudience()
	{
		$resp=$this->xml->createElement('saml:Audience',$this->wtrealm);
		return $resp;
	}

	function createResponseElementLifetime($response_params)
	{
		$resp = $this->xml->createElement('t:Lifetime');
		$resp1=$this->createLifetime($response_params);
		$resp2=$this->expireLifetime($response_params);
		$resp->appendChild($resp1);
		$resp->appendChild($resp2);
		return $resp;
	}

	function createResponseElementAppliesTo($response_params)
	{
		$resp = $this->xml->createElementNS('http://schemas.xmlsoap.org/ws/2004/09/policy','wsp:AppliesTo');
		$resp1= $this->buildAppliesTO($response_params);
		$resp->appendChild($resp1);
		return $resp;
	}

	function buildAppliesTO($response_params)
	{
		$resp = $this->xml->createElementNS('http://www.w3.org/2005/08/addressing','wsa:EndpointReference');
		$resp1= $this->createAddress();
		$resp->appendChild($resp1);
		return $resp;

	}

	function createAddress()
	{
		$resp = $this->xml->createElement('wsa:Address',$this->wtrealm);
		return $resp;
	}

	function createLifetime($response_params)
	{
		$IssueInstant=$response_params['IssueInstant'];
		$resp= $this->xml->createElementNS('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd'
											,"wsu:Created",$IssueInstant);
		return $resp;
	}

	function expireLifetime($response_params)
	{
		$NotOnOrAfter=$response_params['NotOnOrAfter'];
		$resp= $this->xml->createElementNS('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd'
											,"wsu:Expires",$NotOnOrAfter);
		return $resp;
	}
}