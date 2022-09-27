<?php
namespace IDP\Helper\SAML2;

use IDP\Helper\Utilities\SAMLUtilities;


class MetadataReader
{

	private $serviceProviders;

	public function __construct(\DOMNode $xml = NULL)
	{

		$flag = 0;

		$entityDescriptors = SAMLUtilities::xpQuery($xml, './saml_metadata:EntityDescriptor');
		foreach ($entityDescriptors as $entityDescriptor) {
			$spSSODescriptor = SAMLUtilities::xpQuery($entityDescriptor, './saml_metadata:SPSSODescriptor');
			
			if (isset($spSSODescriptor) && !empty($spSSODescriptor)) {
				if (SAMLUtilities::xpQuery($spSSODescriptor[0], './saml_metadata:AssertionConsumerService')) {
					$flag = 1;
				}
			} 
		}

		if ($flag == 0) {
			$this->serviceProviders = NULL;
		}
		else {
			$this->serviceProviders = array();
			$entityDescriptors = SAMLUtilities::xpQuery($xml, './saml_metadata:EntityDescriptor');
			//print_r($entityDescriptors);exit;
	
			foreach ($entityDescriptors as $entityDescriptor) {
				//TODO: add sp descriptor 
				$spSSODescriptor = SAMLUtilities::xpQuery($entityDescriptor, './saml_metadata:SPSSODescriptor');
				
				if (isset($spSSODescriptor) && !empty($spSSODescriptor)) {
					array_push($this->serviceProviders, new ServiceProviders($entityDescriptor));
				} 
			}
		}

	}

	public function getServiceProviders(){
		return $this->serviceProviders;
	}

}

class ServiceProviders{

	private $entityID;
	private $acsUrl;
	private $signedRequest;
	private $nameIDFormat;
	private $logoutDetails;
	private $signingCertificate;
	private $encryptionCertificate;

	public function __construct(\DOMElement $xml = NULL){

		$this->logoutDetails = array();
		$this->signingCertificate = array();
		$this->encryptionCertificate = array();
	

		$spSSODescriptor = SAMLUtilities::xpQuery($xml, './saml_metadata:SPSSODescriptor');

		$this->entityID = $xml->getAttribute('entityID');
		$this->signedRequest = $spSSODescriptor[0]->getAttribute('WantAssertionsSigned');

		$ssoNameID = SAMLUtilities::xpQuery($spSSODescriptor[0], './saml_metadata:NameIDFormat');
        if (!empty($ssoNameID)) {
            $this->nameIDFormat = trim($ssoNameID[0]->nodeValue);
        } else {
			$this->nameIDFormat = "urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress";
		}

		$this->parseACSUrl($spSSODescriptor[0]);
		$this->parseSLOService($spSSODescriptor[0]);
		$this->parsex509Certificate($spSSODescriptor[0]);

	}

	private function parseACSUrl($xml){
		$ssoAcsUrl = SAMLUtilities::xpQuery($xml, './saml_metadata:AssertionConsumerService');
		$this->acsUrl = $ssoAcsUrl[0]->getAttribute('Location');
	}

	private function parseSLOService($xml){
		$sloServices = SAMLUtilities::xpQuery($xml, './saml_metadata:SingleLogoutService');
		foreach ($sloServices as $sloService) {
			$binding = str_replace("urn:oasis:names:tc:SAML:2.0:bindings:", "", $sloService->getAttribute('Binding'));
	        $this->logoutDetails = array_merge( 
	        	$this->logoutDetails, 
	        	array($binding => $sloService->getAttribute('Location')) 
			);
	    }
	}

	private function parsex509Certificate($xml){
		foreach ( SAMLUtilities::xpQuery($xml, './saml_metadata:KeyDescriptor') as $KeyDescriptorNode ) {
			if ($KeyDescriptorNode->hasAttribute('use')) {
				if ($KeyDescriptorNode->getAttribute('use')=='encryption') {
					$this->parseEncryptionCertificate($KeyDescriptorNode);
				} else {
					$this->parseSigningCertificate($KeyDescriptorNode);
				}
			} else {
				$this->parseSigningCertificate($KeyDescriptorNode);
			}
		}
	}

	private function parseSigningCertificate($xml){
		$certNode = SAMLUtilities::xpQuery($xml, './ds:KeyInfo/ds:X509Data/ds:X509Certificate');
		$certData = trim($certNode[0]->textContent);
		$certData = str_replace(array ( "\r", "\n", "\t", ' '), '', $certData);
		if(!empty($certNode))
			array_push($this->signingCertificate, SAMLUtilities::sanitize_certificate($certData));
	}


	private function parseEncryptionCertificate($xml){
		$certNode = SAMLUtilities::xpQuery($xml, './ds:KeyInfo/ds:X509Data/ds:X509Certificate');
		$certData = trim($certNode[0]->textContent);
		$certData = str_replace(array ( "\r", "\n", "\t", ' '), '', $certData);
		if(!empty($certNode))
			array_push($this->encryptionCertificate, $certData);
	}

	public function getEntityID(){
		return $this->entityID;
	}
	public function getAcsURL(){
		return $this->acsUrl;
	}
	public function getSignedRequest(){
		return $this->signedRequest;
	}
	public function getNameID(){
		return $this->nameIDFormat;
	}

	public function getLogoutDetails(){
		return $this->logoutDetails;
	}

	public function getSigningCertificate(){
		return $this->signingCertificate;
	}

	public function getEncryptionCertificate(){
		return $this->encryptionCertificate[0];
	}

}

