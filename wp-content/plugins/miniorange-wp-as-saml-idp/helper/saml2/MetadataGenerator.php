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
	private $new_x509Certificate;
	private $nameIdFormats;
	private $singleSignOnServiceURLs;
	private $singleLogoutServiceURLs;

	function __construct($issuer,$wantAssertionSigned,$x509Certificate,$new_x509Certificate,$ssoURLPost,$ssoURLRedirect,$sloURLPost,$sloURLRedirect)
	{
		$this->xml 						= new \DOMDocument("1.0", "utf-8");
		$this->xml->preserveWhiteSpace 	= FALSE;
		$this->xml->formatOutput 		= TRUE;

		$this->issuer 					= $issuer;			
		$this->wantAssertionSigned 		= $wantAssertionSigned;	
		$this->x509Certificate 			= $x509Certificate;
		$this->new_x509Certificate		= $new_x509Certificate;
		$this->nameIDFormats 			= array("urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress",
												"urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified");
		$this->singleSignOnServiceURLs 	= array("urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"=>$ssoURLPost,
												"urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect"=>$ssoURLRedirect);
		$this->singleLogoutServiceURLs	= array("urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST"=>$sloURLPost,
												"urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect"=>$sloURLRedirect);
	}

	public function generateMetadata()
	{
		//Generating the Metadata Element
		$entity = $this->createEntityDescriptorElement();
		$this->xml->appendChild($entity);

		//Generating the IdpDescriptor Element
		$idpDescriptor = $this->createIdpDescriptorElement();
		$entity->appendChild($idpDescriptor);

		//Generating the Key descriptor element with the new certificate
		if(!get_site_option("mo_idp_new_certs"))
		{
			$key = $this->createNewKeyDescriptorElement();
			$idpDescriptor->appendChild($key);
		}

		//Generate the Key descriptor element for idpDescriptor
		$key3 = $this->createKeyDescriptorElement();
		$idpDescriptor->appendChild($key3);

		//Generate NameID Formats
		$nameIDFormatElements = $this->createNameIdFormatElements();
		foreach ($nameIDFormatElements as $nameIDFormatElement) {
			$idpDescriptor->appendChild($nameIDFormatElement);
		}

		//Generate SingleLogin URL Elements
		$ssoUrlElements = $this->createSSOUrls();
		foreach ($ssoUrlElements as $ssoUrlElement) {
			$idpDescriptor->appendChild($ssoUrlElement);
		}

        //Generate the organization details.
        $orgData = $this->createOrganizationElement();
        $contactdetails = $this->createContactPersonElement();

        $entity->appendChild($orgData);
        $entity->appendChild($contactdetails);

		$metadata = $this->xml->saveXML();
		return $metadata;
	}

	private function createEntityDescriptorElement()
	{
		$entity = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:EntityDescriptor');
		$entity->setAttribute('entityID',esc_attr($this->issuer));
		return $entity;
	}

	private function createIdpDescriptorElement()
	{
		$idpDescriptor = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:IDPSSODescriptor');
		$idpDescriptor->setAttribute('WantAuthnRequestsSigned',esc_attr($this->wantAssertionSigned));
		$idpDescriptor->setAttribute('protocolSupportEnumeration','urn:oasis:names:tc:SAML:2.0:protocol');
		return $idpDescriptor;
	}

	private function createNewKeyDescriptorElement()
	{
		$key = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:KeyDescriptor');
		$key->setAttribute('use',"signing");
		$keyInfo = $this->generateNewKeyInfo();
		$key->appendChild($keyInfo);
		return $key;
	}

	private function createKeyDescriptorElement()
	{
		$key = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:KeyDescriptor');
		$key->setAttribute('use',"signing");
		$keyInfo = $this->generateKeyInfo();
		$key->appendChild($keyInfo);
		return $key;
	}

	private function generateNewKeyInfo()
	{
		$keyInfo = $this->xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:KeyInfo');
		$certdata = $this->xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Data');
		$certValue = SAMLUtilities::desanitize_certificate($this->new_x509Certificate);
		$certElement = $this->xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Certificate', esc_attr($certValue));
		$certdata->appendChild($certElement);
		$keyInfo->appendChild($certdata);
		return $keyInfo;
	}

	private function generateKeyInfo()
	{
		$keyInfo = $this->xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:KeyInfo');
		$certdata = $this->xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Data');
		$certValue = SAMLUtilities::desanitize_certificate($this->x509Certificate);
		$certElement = $this->xml->createElementNS('http://www.w3.org/2000/09/xmldsig#', 'ds:X509Certificate', esc_attr($certValue));
		$certdata->appendChild($certElement);
		$keyInfo->appendChild($certdata);
		return $keyInfo;
	}

	private function createNameIdFormatElements()
	{
		$nameIDFormatElements = array();
		foreach ($this->nameIDFormats as $nameIDFormat) {
			array_push($nameIDFormatElements, $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:NameIDFormat',esc_attr($nameIDFormat)));
		}
		return $nameIDFormatElements;
	}

	private function createSSOUrls()
	{
		$ssoUrlElements = array();
		foreach ($this->singleSignOnServiceURLs as $binding => $url) {
			$ssoURLElement = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:SingleSignOnService');
			$ssoURLElement->setAttribute('Binding',esc_attr($binding));
			$ssoURLElement->setAttribute('Location',esc_url($url));
			array_push($ssoUrlElements,$ssoURLElement);
		}
		return $ssoUrlElements;
	}

	private function createSLOUrls()
	{
		$sloUrlElements = array();
		foreach ($this->singleLogoutServiceURLs as $binding => $url) {
			$sloUrlElement = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:SingleLogoutService');
			$sloUrlElement->setAttribute('Binding',esc_attr($binding));
			$sloUrlElement->setAttribute('Location',esc_url($url));
			array_push($sloUrlElements,$sloUrlElement);
		}
		return $sloUrlElements;
	}

	private function createRoleDescriptorElement()
	{
		$roleDescriptor = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:RoleDescriptor');
		$roleDescriptor->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:xsi','http://www.w3.org/2001/XMLSchema-instance');
		$roleDescriptor->setAttributeNS('http://www.w3.org/2000/xmlns/','xmlns:fed','http://docs.oasis-open.org/wsfed/federation/200706');
        $roleDescriptor->setAttribute('ServiceDisplayName',"miniOrange Inc"); 
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
			$this->xml->createElement('Address',esc_url($this->singleSignOnServiceURLs['urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST'])));
		$passiveRequestEndpoints->appendChild($endpointReference);
		return $passiveRequestEndpoints;
	}
 
    private function createOrganizationElement() 
    { 
        $orgData = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:Organization'); 
        $name = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:OrganizationName','miniOrange'); 
        $name->setAttribute('xml:lang',"en-US"); 
        $displayname = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:OrganizationDisplayName','miniOrange'); 
        $displayname->setAttribute('xml:lang',"en-US"); 
        $orgURL = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:OrganizationURL','https://miniorange.com'); 
        $orgURL->setAttribute('xml:lang',"en-US"); 
        $orgData->appendChild($name); 
        $orgData->appendChild($displayname); 
        $orgData->appendChild($orgURL); 
     
        return $orgData; 
    } 
 
    private function createContactPersonElement(){
        $contactDetails = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:ContactPerson');
        $contactDetails->setAttribute('contactType','technical');
        $name = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:GivenName','miniOrange');
        $surname = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:SurName','Support');
        $email = $this->xml->createElementNS('urn:oasis:names:tc:SAML:2.0:metadata','md:EmailAddress','info@xecurify.com');
        $contactDetails->appendChild($name);
        $contactDetails->appendChild($surname);
        $contactDetails->appendChild($email);

        return $contactDetails;
    }
}