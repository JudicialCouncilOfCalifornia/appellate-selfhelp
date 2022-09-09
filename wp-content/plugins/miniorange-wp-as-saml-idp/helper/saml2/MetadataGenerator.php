<?php 

namespace IDP\Helper\SAML2;

use IDP\Helper\Utilities\MoIDPUtility;
use IDP\Helper\Utilities\SAMLUtilities;

class MetadataGenerator
{
	private $xml;
	private $issuer;
	private $samlLoginURL;
	private $wantAssertionSigned;
	private $x509Certificate;
	private $nameIdFormats;
	private $singleSignOnServiceURLs;
	private $singleLogoutServiceURLs;

	function __construct($issuer,$wantAssertionSigned,$x509Certificate,$ssoURLPost,$ssoURLRedirect,$sloURLPost,$sloURLRedirect)
	{
		$this->xml 						= new \DOMDocument("1.0", "utf-8");
		$this->xml->preserveWhiteSpace 	= FALSE;
		$this->xml->formatOutput 		= TRUE;

		$this->issuer 					= $issuer;			
		$this->wantAssertionSigned 		= $wantAssertionSigned;	
		$this->x509Certificate 			= $x509Certificate;
		$this->nameIDFormats 			= array("urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress",
												"urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified");
		$this->singleSignOnServiceURLs 	= array("urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"=>$ssoURLPost,
												"urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect"=>$ssoURLRedirect);
		$this->singleLogoutServiceURLs	= array("urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"=>$sloURLPost,
												"urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect"=>$sloURLRedirect);
	}

	public function generateMetadata()
	{
				$entity = $this->createEntityDescriptorElement();
		$this->xml->appendChild($entity);

				$idpDescriptor = $this->createIdpDescriptorElement();
		$entity->appendChild($idpDescriptor);

				$roleDescriptor = $this->createRoleDescriptorElement();
		$entity->appendChild($roleDescriptor);

				$key = $this->createKeyDescriptorElement();
		$idpDescriptor->appendChild($key);

				$key2 = $this->createKeyDescriptorElement();
		$roleDescriptor->appendChild($key2);

				$tokenTypes = $this->createTokenTypesElement();
		$roleDescriptor->appendChild($tokenTypes);

				$passiveRequestEndpoints = $this->createPassiveRequestEndpoints();
		$roleDescriptor->appendChild($passiveRequestEndpoints);

				$nameIDFormatElements = $this->createNameIdFormatElements();
		foreach ($nameIDFormatElements as $nameIDFormatElement) {
			$idpDescriptor->appendChild($nameIDFormatElement);
		}

				$ssoUrlElements = $this->createSSOUrls();
		foreach ($ssoUrlElements as $ssoUrlElement) {
			$idpDescriptor->appendChild($ssoUrlElement);
		}

				$sloUrlElements = $this->createSLOUrls();
		foreach ($sloUrlElements as $sloUrlElement) {
			$idpDescriptor->appendChild($sloUrlElement);
		}

		$metadata = $this->xml->saveXML();
		return $metadata;
	}

	private function createEntityDescriptorElement()
	{
		$entity = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:EntityDescriptor');
		$entity->setAttribute('entityID',$this->issuer);
		return $entity;
	}

	private function createIdpDescriptorElement()
	{
		$idpDescriptor = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:IDPSSODescriptor');
		$idpDescriptor->setAttribute('WantAuthnRequestsSigned',$this->wantAssertionSigned);
		$idpDescriptor->setAttribute('protocolSupportEnumeration','urn:oasis:names:tc:SAML:2.0:protocol');
		return $idpDescriptor;
	}

	private function createKeyDescriptorElement()
	{
		$key = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:KeyDescriptor');
		$key->setAttribute('use',"signing");
		$keyInfo = $this->generateKeyInfo();
		$key->appendChild($keyInfo);
		return $key;
	}

	private function generateKeyInfo()
	{
		$keyInfo = $this->xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:KeyInfo');
		$certdata = $this->xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Data');
		$certValue = SAMLUtilities::desanitize_certificate($this->x509Certificate);
		$certElement = $this->xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Certificate', $certValue);
		$certdata->appendChild($certElement);
		$keyInfo->appendChild($certdata);
		return $keyInfo;
	}

	private function createNameIdFormatElements()
	{
		$nameIDFormatElements = array();
		foreach ($this->nameIDFormats as $nameIDFormat) {
			array_push($nameIDFormatElements, $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:NameIDFormat',$nameIDFormat));
		}
		return $nameIDFormatElements;
	}

	private function createSSOUrls()
	{
		$ssoUrlElements = array();
		foreach ($this->singleSignOnServiceURLs as $binding => $url) {
			$ssoURLElement = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:SingleSignOnService');
			$ssoURLElement->setAttribute('Binding',$binding);
			$ssoURLElement->setAttribute('Location',$url);
			array_push($ssoUrlElements,$ssoURLElement);
		}
		return $ssoUrlElements;
	}

	private function createSLOUrls()
	{
		$sloUrlElements = array();
		foreach ($this->singleLogoutServiceURLs as $binding => $url) {
			$sloUrlElement = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:SingleLogoutService');
			$sloUrlElement->setAttribute('Binding',$binding);
			$sloUrlElement->setAttribute('Location',$url);
			array_push($sloUrlElements,$sloUrlElement);
		}
		return $sloUrlElements;
	}

	private function createRoleDescriptorElement()
	{
		$roleDescriptor = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:RoleDescriptor');
		$roleDescriptor->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
		$roleDescriptor->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:fed','http://docs.oasis-open.org/wsfed/federation/200706');
		$roleDescriptor->setAttribute('ServiceDisplayName',"miniOrnage Inc");
		$roleDescriptor->setAttribute('xsi:type',"fed:SecurityTokenServiceType");
		$roleDescriptor->setAttribute('protocolSupportEnumeration',"http://docs.oasis-open.org/ws-sx/ws-trust/200512 http://schemas.xmlsoap.org/ws/2005/02/trust http://docs.oasis-open.org/wsfed/federation/200706");
		return $roleDescriptor;
	}

	private function createTokenTypesElement()
	{
		$tokenTypes = $this->xml->createElement('fed:TokenTypesOffered');
		$samlToken = $this->xml->createElement('fed:TokenType');
		$samlToken->setAttribute('Uri','urn:oasis:names:tc:SAML:1.0:assertion');
		$tokenTypes->appendChild($samlToken);
		return $tokenTypes;
	}

	private function createPassiveRequestEndpoints()
	{
		$passiveRequestEndpoints = $this->xml->createElement('fed:PassiveRequestorEndpoint');
		$endpointReference = $this->xml->createElementNS('http://www.w3.org/2005/08/addressing','ad:EndpointReference');
		$endpointReference->appendChild(
			$this->xml->createElement('Address',$this->singleSignOnServiceURLs['urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST']));
		$passiveRequestEndpoints->appendChild($endpointReference);
		return $passiveRequestEndpoints;
	}
}