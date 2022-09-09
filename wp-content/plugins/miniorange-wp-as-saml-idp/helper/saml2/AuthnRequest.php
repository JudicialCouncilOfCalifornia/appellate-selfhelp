<?php

namespace IDP\Helper\SAML2;

use IDP\Helper\Utilities\SAMLUtilities;
use IDP\Helper\Constants\MoIDPConstants;
use IDP\Helper\Factory\RequestHandlerFactory;
use IDP\Exception\InvalidRequestInstantException;
use IDP\Exception\InvalidRequestVersionException;
use IDP\Exception\MissingIssuerValueException;

class AuthnRequest implements RequestHandlerFactory
{   
    private $xml;
    private $nameIdPolicy;
    private $forceAuthn;
    private $isPassive;
    private $RequesterID = array();
    private $assertionConsumerServiceURL;
    private $protocolBinding;
    private $requestedAuthnContext;
    private $namespaceURI;
    private $destination;
    private $issuer;
    private $version;
    private $issueInstant;
    private $requestID;
    private $requestType = MoIDPConstants::AUTHN_REQUEST;

    public function __construct(\DOMElement $xml = null)
    {
        $this->nameIdPolicy = array();
        $this->forceAuthn = false;
        $this->isPassive = false;
        if ($xml === null) return;

        $this->xml = $xml;
        $this->forceAuthn = SAMLUtilities::parseBoolean($xml, 'ForceAuthn', false);
        $this->isPassive = SAMLUtilities::parseBoolean($xml, 'IsPassive', false);

        if ($xml->hasAttribute('AssertionConsumerServiceURL')) 
            $this->assertionConsumerServiceURL = $xml->getAttribute('AssertionConsumerServiceURL');

        if ($xml->hasAttribute('ProtocolBinding')) 
            $this->protocolBinding = $xml->getAttribute('ProtocolBinding');

        if ($xml->hasAttribute('AttributeConsumingServiceIndex')) 
            $this->attributeConsumingServiceIndex = (int) $xml->getAttribute('AttributeConsumingServiceIndex');

        if ($xml->hasAttribute('AssertionConsumerServiceIndex')) 
            $this->assertionConsumerServiceIndex = (int) $xml->getAttribute('AssertionConsumerServiceIndex');

        if ($xml->hasAttribute('Destination')) 
            $this->destination = $xml->getAttribute('Destination');

        if (isset($xml->namespaceURI)) 
            $this->namespaceURI = $xml->namespaceURI;

        if ($xml->hasAttribute('Version')) 
            $this->version = $xml->getAttribute('Version');

        if ($xml->hasAttribute('IssueInstant')) 
            $this->issueInstant = $xml->getAttribute('IssueInstant');

        if ($xml->hasAttribute('ID')) 
            $this->requestID = $xml->getAttribute('ID');

        $this->checkAuthnRequestIssueInstant();
        $this->checkSAMLRequestVersion();
        $this->parseNameIdPolicy($xml);
        $this->parseIssuer($xml);
        $this->parseRequestedAuthnContext($xml);
        $this->parseScoping($xml);
    }

    protected function parseIssuer(\DOMElement $xml)
    {
        $issuer = SAMLUtilities::xpQuery($xml, './saml_assertion:Issuer');
        if (empty($issuer)) {
            throw new MissingIssuerValueException();
        }
        $this->issuer = trim($issuer[0]->textContent);
    }

    protected function parseNameIdPolicy(\DOMElement $xml)
    {
        $nameIdPolicy = SAMLUtilities::xpQuery($xml, './saml_protocol:NameIDPolicy');
        if (empty($nameIdPolicy)) {
            return;
        }
        $nameIdPolicy = $nameIdPolicy[0];
        if ($nameIdPolicy->hasAttribute('Format')) {
            $this->nameIdPolicy['Format'] = $nameIdPolicy->getAttribute('Format');
        }
        if ($nameIdPolicy->hasAttribute('SPNameQualifier')) {
            $this->nameIdPolicy['SPNameQualifier'] = $nameIdPolicy->getAttribute('SPNameQualifier');
        }
        if ($nameIdPolicy->hasAttribute('AllowCreate')) {
            $this->nameIdPolicy['AllowCreate'] = SAMLUtilities::parseBoolean($nameIdPolicy, 'AllowCreate', false);
        }
    }

    protected function parseRequestedAuthnContext(\DOMElement $xml)
    {
        $requestedAuthnContext = SAMLUtilities::xpQuery($xml, './saml_protocol:RequestedAuthnContext');
        if (empty($requestedAuthnContext)) {
            return;
        }
        $requestedAuthnContext = $requestedAuthnContext[0];
        $rac = array(
            'AuthnContextClassRef' => array(),
            'Comparison'           => 'exact',
        );
        $accr = SAMLUtilities::xpQuery($requestedAuthnContext, './saml_assertion:AuthnContextClassRef');
        foreach ($accr as $i) {
            $rac['AuthnContextClassRef'][] = trim($i->textContent);
        }
        if ($requestedAuthnContext->hasAttribute('Comparison')) {
            $rac['Comparison'] = $requestedAuthnContext->getAttribute('Comparison');
        }
        $this->requestedAuthnContext = $rac;
    }

    protected function parseScoping(\DOMElement $xml)
    {
        $scoping = SAMLUtilities::xpQuery($xml, './saml_protocol:Scoping');
        if (empty($scoping)) {
            return;
        }
        $scoping = $scoping[0];
        if ($scoping->hasAttribute('ProxyCount')) {
            $this->ProxyCount = (int) $scoping->getAttribute('ProxyCount');
        }
        $idpEntries = SAMLUtilities::xpQuery($scoping, './saml_protocol:IDPList/saml_protocol:IDPEntry');
        foreach ($idpEntries as $idpEntry) {
            if (!$idpEntry->hasAttribute('ProviderID')) {
                throw new \Exception("Could not get ProviderID from Scoping/IDPEntry element in AuthnRequest object");
            }
            $this->IDPList[] = $idpEntry->getAttribute('ProviderID');
        }
        $requesterIDs = SAMLUtilities::xpQuery($scoping, './saml_protocol:RequesterID');
        foreach ($requesterIDs as $requesterID) {
            $this->RequesterID[] = trim($requesterID->textContent);
        }
    }

    public function checkAuthnRequestIssueInstant()
    {
        if(strtotime($this->issueInstant) >= time()+60) throw new InvalidRequestInstantException();
    }

    public function checkSAMLRequestVersion()
    {
        if($this->version!=='2.0') throw new InvalidRequestVersionException();
    }

    public function generateRequest()
    {
        return;
    }

    public function __toString()
    {
        $html = '[ AUTHN REQUEST PARAMS';
        $html .= ', NamespaceURI = '.$this->namespaceURI;
        $html .= ', ProtocolBinding = '.$this->protocolBinding;
        $html .= ', ID = '.$this->requestID;
        $html .= ', Issuer = '.$this->issuer;
        $html .= ', ACS URL = '.$this->assertionConsumerServiceURL;
        $html .= ', Destination = '.$this->destination;
        $html .= ', Format = '.implode(",",$this->nameIdPolicy);
        $html .= ', Allow Create = '.implode(",",$this->nameIdPolicy);
        $html .= ', Force Authn = '.$this->forceAuthn;
        $html .= ', Issue Instant = '.$this->issueInstant;
        $html .= ', Version = '.$this->version;
        $html .= ', RequesterID = '. implode(",",$this->RequesterID);
        $html .= ']';
        return $html;
    }

    public function getXml()
    {
        return $this->xml;
    }

    public function setXml($xml)
    {
        $this->xml = $xml;

        return $this;
    }

    public function getNameIdPolicy()
    {
        return $this->nameIdPolicy;
    }

    public function setNameIdPolicy($nameIdPolicy)
    {
        $this->nameIdPolicy = $nameIdPolicy;

        return $this;
    }

    public function getForceAuthn()
    {
        return $this->forceAuthn;
    }

    public function setForceAuthn($forceAuthn)
    {
        $this->forceAuthn = $forceAuthn;

        return $this;
    }

    public function getIsPassive()
    {
        return $this->isPassive;
    }

    public function setIsPassive($isPassive)
    {
        $this->isPassive = $isPassive;

        return $this;
    }

    public function getRequesterID()
    {
        return $this->RequesterID;
    }

    public function setRequesterID($RequesterID)
    {
        $this->RequesterID = $RequesterID;

        return $this;
    }

    public function getAssertionConsumerServiceURL()
    {
        return $this->assertionConsumerServiceURL;
    }

    public function setAssertionConsumerServiceURL($assertionConsumerServiceURL)
    {
        $this->assertionConsumerServiceURL = $assertionConsumerServiceURL;

        return $this;
    }

    public function getProtocolBinding()
    {
        return $this->protocolBinding;
    }

    public function setProtocolBinding($protocolBinding)
    {
        $this->protocolBinding = $protocolBinding;

        return $this;
    }

    public function getRequestedAuthnContext()
    {
        return $this->requestedAuthnContext;
    }

    public function setRequestedAuthnContext($requestedAuthnContext)
    {
        $this->requestedAuthnContext = $requestedAuthnContext;

        return $this;
    }

    public function getNamespaceURI()
    {
        return $this->namespaceURI;
    }

    public function setNamespaceURI($namespaceURI)
    {
        $this->namespaceURI = $namespaceURI;

        return $this;
    }

    public function getDestination()
    {
        return $this->destination;
    }

    public function setDestination($destination)
    {
        $this->destination = $destination;

        return $this;
    }

    public function getIssuer()
    {
        return $this->issuer;
    }

    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;

        return $this;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    public function getIssueInstant()
    {
        return $this->issueInstant;
    }

    public function setIssueInstant($issueInstant)
    {
        $this->issueInstant = $issueInstant;

        return $this;
    }

    public function getRequestID()
    {
        return $this->requestID;
    }

    public function setRequestID($requestID)
    {
        $this->requestID = $requestID;

        return $this;
    }

    public function getRequestType()
    {
        return $this->requestType;
    }

    public function setRequestType($requestType)
    {
        $this->requestType = $requestType;

        return $this;
    }
}