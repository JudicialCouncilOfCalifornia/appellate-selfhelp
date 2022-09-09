<?php

namespace IDP\Helper\SAML2;

use IDP\Helper\Utilities\MoIDPUtility;
use IDP\Helper\Utilities\SAMLUtilities;
use \RobRichards\XMLSecLibs\XMLSecurityKey;
use \RobRichards\XMLSecLibs\XMLSecurityDSig;
use \RobRichards\XMLSecLibs\XMLSecEnc;
use IDP\Helper\Factory\ResponseHandlerFactory;

class GenerateResponse implements ResponseHandlerFactory
{
	private $xml;
	private $acsUrl;
	private $issuer;
	private $audience;
	private $username;
	private $email;
	private $sp;
	private $sp_attr;
	private $requestID;
	private $subject;
	private $mo_idp_assertion_signed;
	private $mo_idp_encrypted_assertion;
	private $mo_idp_response_signed;
	private $mo_idp_nameid_attr;
	private $mo_idp_nameid_format;
	private $mo_idp_cert_encrypt;
	private $login;

	function __construct($acs_url, $issuer, $audience, $requestID, $sp_attr, $sp, $login)
	{
		$this->xml = new \DOMDocument("1.0", "utf-8");
		$this->acsUrl = $acs_url;
		$this->issuer = $issuer;
		$this->audience = $audience;
		$this->requestID=$requestID;
		$this->login = $login;
		$this->sp_attr = $sp_attr;
		$this->mo_idp_nameid_format = $sp->mo_idp_nameid_format;
		$this->mo_idp_assertion_signed = $sp->mo_idp_assertion_signed;
		$this->mo_idp_encrypted_assertion = $sp->mo_idp_encrypted_assertion;
		$this->mo_idp_response_signed = $sp->mo_idp_response_signed;
		$this->mo_idp_nameid_attr = $sp->mo_idp_nameid_attr;
		$this->mo_idp_cert_encrypt = $sp->mo_idp_cert_encrypt;
		$this->current_user = is_null($this->login) ? wp_get_current_user() : get_user_by('login',$this->login);
	}

	function generateResponse()
	{
		if(MoIDPUtility::isBlank($this->current_user)) throw new InvalidSSOUserException();
		$response_params = $this->getResponseParams();

				$resp = $this->createResponseElement($response_params);
		$this->xml->appendChild($resp);

				$issuer = $this->buildIssuer();
		$resp->appendChild($issuer);

				$status = $this->buildStatus();
		$resp->appendChild($status);
		$statusCode = $this->buildStatusCode();
		$status->appendChild($statusCode);

				$assertion = $this->buildAssertion($response_params);
		$resp->appendChild($assertion);

				if($this->mo_idp_assertion_signed)
		{
			$private_key = MoIDPUtility::getPrivateKey();
			$this->signNode($private_key, $assertion, $this->subject, $response_params);
		}

		$samlResponse = $this->xml->saveXML();

		return $samlResponse;
	}

	function getResponseParams()
	{
		$response_params = array();
		$time = time();
		$response_params['IssueInstant'] = str_replace('+00:00','Z',gmdate("c",$time));
		$response_params['NotOnOrAfter'] = str_replace('+00:00','Z',gmdate("c",$time+300));
		$response_params['NotBefore'] = str_replace('+00:00','Z',gmdate("c",$time-30));
		$response_params['AuthnInstant'] = str_replace('+00:00','Z',gmdate("c",$time-120));
		$response_params['SessionNotOnOrAfter'] = str_replace('+00:00','Z',gmdate("c",$time+3600*8));
		$response_params['ID'] = $this->generateUniqueID(40);
		$response_params['AssertID'] = $this->generateUniqueID(40);
		$response_params['Issuer'] = $this->issuer;
		$public_key = MoIDPUtility::getPublicCert();
		$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256,array( 'type' => 'public'));
		$objKey->loadKey($public_key, FALSE,TRUE);
		$response_params['x509'] = $objKey->getX509Certificate();
		return $response_params;
	}

	function createResponseElement($response_params)
	{
		$resp = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:protocol','samlp:Response');
		$resp->setAttribute('ID',$response_params['ID']);
		$resp->setAttribute('Version','2.0');
		$resp->setAttribute('IssueInstant',$response_params['IssueInstant']);
		$resp->setAttribute('Destination',$this->acsUrl);
		if(!is_null($this->requestID))
			$resp->setAttribute('InResponseTo',$this->requestID);
		return $resp;
	}

	function buildIssuer()
	{
		$issuer = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:assertion','saml:Issuer',$this->issuer);
		return $issuer;
	}

	function buildStatus()
	{
		$status = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:protocol','samlp:Status');
		return $status;
	}

	function buildStatusCode()
	{
		$statusCode = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:protocol','samlp:StatusCode');
		$statusCode->setAttribute('Value', 'urn:oasis:names:tc:SAML:2.0:status:Success');
		return $statusCode;
	}

	function buildAssertion($response_params)
	{
		$assertion = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:assertion','saml:Assertion');
		$assertion->setAttribute('ID',$response_params['AssertID']);
		$assertion->setAttribute('IssueInstant',$response_params['IssueInstant']);
		$assertion->setAttribute('Version','2.0');

				$issuer = $this->buildIssuer($response_params);
		$assertion->appendChild($issuer);

				$subject = $this->buildSubject($response_params);
		$this->subject = $subject;
		$assertion->appendChild($subject);

				$condition = $this->buildCondition($response_params);
		$assertion->appendChild($condition);

				$authnstat = $this->buildAuthnStatement($response_params);
		$assertion->appendChild($authnstat);

		return $assertion;
	}

	function buildSubject($response_params)
	{
		$subject = $this->xml->createElement('saml:Subject');
		$nameid = $this->buildNameIdentifier();
		$subject->appendChild($nameid);
		$confirmation = $this->buildSubjectConfirmation($response_params);
		$subject->appendChild($confirmation);
		return $subject;
	}

	function buildNameIdentifier()
	{
		$nameID = !empty($this->mo_idp_nameid_attr) && $this->mo_idp_nameid_attr!='emailAddress' ? $this->mo_idp_nameid_attr : "user_email";
		$value = MoIDPUtility::isBlank($this->current_user->$nameID)
					? get_user_meta($this->current_user->ID, $nameID, true) : $this->current_user->$nameID;

		$value = apply_filters('generate_saml_attribute_value',$value,$this->current_user,'NameID');

		$nameid = $this->xml->createElement("saml:NameID", $value);
		$nameid->setAttribute('Format','urn:oasis:names:tc:SAML:'.$this->mo_idp_nameid_format);
				return $nameid;
	}

	function buildSubjectConfirmation($response_params)
	{
		$confirmation = $this->xml->createElement('saml:SubjectConfirmation');
		$confirmation->setAttribute('Method','urn:oasis:names:tc:SAML:2.0:cm:bearer');
		$confirmationdata = $this->getSubjectConfirmationData($response_params);
		$confirmation->appendChild($confirmationdata);
		return $confirmation;
	}

	function getSubjectConfirmationData($response_params)
	{
		$confirmationdata = $this->xml->createElement('saml:SubjectConfirmationData');
		$confirmationdata->setAttribute('NotOnOrAfter',$response_params['NotOnOrAfter']);
		$confirmationdata->setAttribute('Recipient',$this->acsUrl);
		if(!is_null($this->requestID))
			$confirmationdata->setAttribute('InResponseTo',$this->requestID);
		return $confirmationdata;
	}

	function buildCondition($response_params)
	{
		$condition = $this->xml->createElement('saml:Conditions');
		$condition->setAttribute('NotBefore',$response_params['NotBefore']);
		$condition->setAttribute('NotOnOrAfter',$response_params['NotOnOrAfter']);

				$audiencer = $this->buildAudienceRestriction();
		$condition->appendChild($audiencer);

		return $condition;
	}

	function buildAudienceRestriction()
    {
        $audienceRestriction = $this->xml->createElement('saml:AudienceRestriction');
        $audience = $this->xml->createElement('saml:Audience',$this->audience);
        $audienceRestriction->appendChild($audience);
        return $audienceRestriction;
    }

	function buildAuthnStatement($response_params)
	{
		$authnstat = $this->xml->createElement('saml:AuthnStatement');
		$authnstat->setAttribute('AuthnInstant',$response_params['AuthnInstant']);
		$authnstat->setAttribute('SessionIndex','_'.$this->generateUniqueID(30));
		$authnstat->setAttribute('SessionNotOnOrAfter',$response_params['SessionNotOnOrAfter']);

		$authncontext = $this->xml->createElement('saml:AuthnContext');
		$authncontext_ref = $this->xml->createElement('saml:AuthnContextClassRef','urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport');
		$authncontext->appendChild($authncontext_ref);
		$authnstat->appendChild($authncontext);

		return $authnstat;
	}

	function signNode($private_key, $node, $subject,$response_params)
	{
				$objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256,array( 'type' => 'private'));
		$objKey->loadKey($private_key, FALSE);

				$objXMLSecDSig = new XMLSecurityDSig();
		$objXMLSecDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);

		$objXMLSecDSig->addReferenceList(array($node), XMLSecurityDSig::SHA1,
			array('http://www.w3.org/2000/09/xmldsig#enveloped-signature', XMLSecurityDSig::EXC_C14N),array('id_name'=>'ID','overwrite'=>false));
		$objXMLSecDSig->sign($objKey);
		$objXMLSecDSig->add509Cert($response_params['x509']);
				
		$objXMLSecDSig->insertSignature($node,$subject);
	}

	function generateUniqueID($length)
	{
		return MoIDPUtility::generateRandomAlphanumericValue($length);
	}

}