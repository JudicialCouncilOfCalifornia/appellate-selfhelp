<?php

namespace IDP\Helper\Utilities;

use \RobRichards\XMLSecLibs\XMLSecurityDSig;
use \RobRichards\XMLSecLibs\XMLSecurityKey;

use IDP\Helper\Factory\ResponseDecisionHandler;
use IDP\Helper\Factory\RequestDecisionHandler;
use IDP\Helper\Constants\MoIDPConstants;

class SAMLUtilities {

	public static function generateID()
    {
		return '_' . self::stringToHex(self::generateRandomBytes(21));
	}

	public static function stringToHex($bytes)
    {
		$ret = '';
		for($i = 0; $i < strlen($bytes); $i++) {
			$ret .= sprintf('%02x', ord($bytes[$i]));
		}
		return $ret;
	}

	public static function generateRandomBytes($length, $fallback = TRUE)
    {
        return openssl_random_pseudo_bytes($length);
	}

	public static function generateTimestamp($instant = NULL)
    {
		if($instant === NULL) {
			$instant = time();
		}
		return gmdate('Y-m-d\TH:i:s\Z', $instant);
	}

	public static function xpQuery(\DomNode$node, $query)
    {
        static $xpCache = NULL;

        if ($node instanceof \DOMDocument) {
            $doc = $node;
        } else {
            $doc = $node->ownerDocument;
        }

        if ($xpCache === NULL || !$xpCache->document->isSameNode($doc)) {
            $xpCache = new \DOMXPath($doc);
            $xpCache->registerNamespace('soap-env', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xpCache->registerNamespace('saml_protocol', 'urn:oasis:names:tc:SAML:2.0:protocol');
            $xpCache->registerNamespace('saml_assertion', 'urn:oasis:names:tc:SAML:2.0:assertion');
            $xpCache->registerNamespace('saml_metadata', 'urn:oasis:names:tc:SAML:2.0:metadata');
            $xpCache->registerNamespace('ds', 'http://www.w3.org/2000/09/xmldsig#');
            $xpCache->registerNamespace('xenc', 'http://www.w3.org/2001/04/xmlenc#');
        }

        $results = $xpCache->query($query, $node);
        $ret = array();
        for ($i = 0; $i < $results->length; $i++) {
            $ret[$i] = $results->item($i);
        }

		return $ret;
    }

	public static function parseNameId(\DOMElement $xml)
    {
        $ret = array('Value' => trim($xml->textContent));

        foreach (array('NameQualifier', 'SPNameQualifier', 'Format') as $attr) {
            if ($xml->hasAttribute($attr)) {
                $ret[$attr] = $xml->getAttribute($attr);
            }
        }

        return $ret;
    }

	public static function xsDateTimeToTimestamp($time)
    {
        $matches = array();

                $regex = '/^(\\d\\d\\d\\d)-(\\d\\d)-(\\d\\d)T(\\d\\d):(\\d\\d):(\\d\\d)(?:\\.\\d+)?Z$/D';
        if (preg_match($regex, $time, $matches) == 0) {
            echo sprintf("nvalid SAML2 timestamp passed to xsDateTimeToTimestamp: ".$time);
            exit;
        }

                        $year   = intval($matches[1]);
        $month  = intval($matches[2]);
        $day    = intval($matches[3]);
        $hour   = intval($matches[4]);
        $minute = intval($matches[5]);
        $second = intval($matches[6]);

                        $ts = gmmktime($hour, $minute, $second, $month, $day, $year);

        return $ts;
    }

	public static function extractStrings(\DOMElement $parent, $namespaceURI, $localName)
    {
        $ret = array();
        for ($node = $parent->firstChild; $node !== NULL; $node = $node->nextSibling) {
            if ($node->namespaceURI !== $namespaceURI || $node->localName !== $localName) {
                continue;
            }
            $ret[] = trim($node->textContent);
        }

        return $ret;
    }

	public static function validateElement(\DOMElement $root)
    {
    	    	
        $objXMLSecDSig = new XMLSecurityDSig();

        $objXMLSecDSig->idKeys[] = 'ID';

        $signatureElement = self::xpQuery($root, './ds:Signature');

        if (count($signatureElement) === 0) {

            return FALSE;
        } elseif (count($signatureElement) > 1) {
        	echo sprintf("XMLSec: more than one signature element in root.");
        	exit;
        }

        $signatureElement = $signatureElement[0];
        $objXMLSecDSig->sigNode = $signatureElement;

        $objXMLSecDSig->canonicalizeSignedInfo();

        if (!$objXMLSecDSig->validateReference()) {
        	echo sprintf("XMLsec: digest validation failed");
        	exit;
        }

        $rootSigned = FALSE;

        foreach ($objXMLSecDSig->getValidatedNodes() as $signedNode) {
            if ($signedNode->isSameNode($root)) {
                $rootSigned = TRUE;
                break;
            } elseif ($root->parentNode instanceof \DOMDocument && $signedNode->isSameNode($root->ownerDocument)) {

                $rootSigned = TRUE;
                break;
            }
        }

		if (!$rootSigned) {
			echo sprintf("XMLSec: The root element is not signed.");
			exit;
        }

        $certificates = array();
        foreach (self::xpQuery($signatureElement, './ds:KeyInfo/ds:X509Data/ds:X509Certificate') as $certNode) {
            $certData = trim($certNode->textContent);
            $certData = str_replace(array("\r", "\n", "\t", ' '), '', $certData);
            $certificates[] = $certData;
			        }

        $ret = array(
            'Signature' => $objXMLSecDSig,
            'Certificates' => $certificates,
            );

		
        return $ret;
    }

	public static function validateSignature(array $info, XMLSecurityKey $key)
    {

        $objXMLSecDSig = $info['Signature'];

        $sigMethod = self::xpQuery($objXMLSecDSig->sigNode, './ds:SignedInfo/ds:SignatureMethod');
        if (empty($sigMethod)) {
            echo sprintf('Missing SignatureMethod element');
            exit();
        }
        $sigMethod = $sigMethod[0];
        if (!$sigMethod->hasAttribute('Algorithm')) {
            echo sprintf('Missing Algorithm-attribute on SignatureMethod element.');
            exit;
        }
        $algo = $sigMethod->getAttribute('Algorithm');

        if ($key->type === XMLSecurityKey::RSA_SHA256 && $algo !== $key->type) {
            $key = self::castKey($key, $algo);
        }

        if (! $objXMLSecDSig->verify($key)) {
        	echo sprintf('Unable to validate Sgnature');
        	exit;
        }
    }

    public static function castKey(XMLSecurityKey $key, $algorithm, $type = 'public')
    {

    	    	if ($key->type === $algorithm) {
    		return $key;
    	}

    	$keyInfo = openssl_pkey_get_details($key->key);
    	if ($keyInfo === FALSE) {
    		echo sprintf('Unable to get key details from XMLSecurityKey.');
    		exit;
    	}
    	if (!isset($keyInfo['key'])) {
    		echo sprintf('Missing key in public key details.');
    		exit;
    	}

    	$newKey = new XMLSecurityKey($algorithm, array('type'=>$type));
    	$newKey->loadKey($keyInfo['key']);

    	return $newKey;
    }

    public static function processRequest($certFingerprint, $signatureData)
    {

        $responseSigned = self::checkSign($certFingerprint, $signatureData);

        return $responseSigned;
    }

	public static function checkSign($certFingerprint, $signatureData)
    {
		$certificates = $signatureData['Certificates'];

		if (count($certificates) === 0) {
			return FALSE;
		}

		$fpArray = array();
		$fpArray[] = $certFingerprint;
		$pemCert = self::findCertificate($fpArray, $certificates);

		$lastException = NULL;

		$key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array('type'=>'public'));
		$key->loadKey($pemCert);

		try {

			self::validateSignature($signatureData, $key);
			return TRUE;
		} catch (Exception $e) {
			$lastException = $e;
		}

		if ($lastException !== NULL) {
			throw $lastException;
		} else {
			return FALSE;
		}

	}

	private static function findCertificate(array $certFingerprints, array $certificates)
    {
		$candidates = array();

		foreach ($certificates as $cert) {
			$fp = strtolower(sha1(base64_decode($cert)));
			if (!in_array($fp, $certFingerprints, TRUE)) {
				$candidates[] = $fp;
				continue;
			}

			$pem = "-----BEGIN CERTIFICATE-----\n" .
				chunk_split($cert, 64) .
				"-----END CERTIFICATE-----\n";

			return $pem;
		}

		echo sprintf('Unable to find a certificate matching the configured fingerprint.');
		exit;
	}

    public static function parseBoolean(\DOMElement $node, $attributeName, $default = null)
    {
        if (!$node->hasAttribute($attributeName)) {
            return $default;
        }
        $value = $node->getAttribute($attributeName);
        switch (strtolower($value)) {
            case '0':
            case 'false':
                return false;
            case '1':
            case 'true':
                return true;
            default:
                throw new \Exception('Invalid value of boolean attribute ' . var_export($attributeName, true) . ': ' . var_export($value, true));
        }
    }

    public static function insertSignature(
        XMLSecurityKey $key,
        array $certificates,
        \DOMElement $root,
        \DomNode $insertBefore = NULL
    ) {
        $objXMLSecDSig = new XMLSecurityDSig();
        $objXMLSecDSig->setCanonicalMethod(XMLSecurityDSig::EXC_C14N);

        switch ($key->type) {
            case XMLSecurityKey::RSA_SHA256:
                $type = XMLSecurityDSig::SHA256;
                break;
            case XMLSecurityKey::RSA_SHA384:
                $type = XMLSecurityDSig::SHA384;
                break;
            case XMLSecurityKey::RSA_SHA512:
                $type = XMLSecurityDSig::SHA512;
                break;
            default:
                $type = XMLSecurityDSig::SHA1;
        }

        $objXMLSecDSig->addReferenceList(
            array($root),
            $type,
            array('http://www.w3.org/2000/09/xmldsig#enveloped-signature', XMLSecurityDSig::EXC_C14N),
            array('id_name' => 'ID', 'overwrite' => FALSE)
        );

        $objXMLSecDSig->sign($key);

        foreach ($certificates as $certificate) {
            $objXMLSecDSig->add509Cert($certificate, TRUE);
        }
        $objXMLSecDSig->insertSignature($root, $insertBefore);
    }

    public static function signXML($xml, $publicCertPath, $privateKeyPath, $insertBeforeTagName = "")
    {
        $param =array( 'type' => 'private');
        $key = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, $param);
        $key->loadKey($privateKeyPath, TRUE);
        $publicCertificate = file_get_contents( $publicCertPath );
        $document = new \DOMDocument();
        $document->loadXML($xml);
        $element = $document->firstChild;
        if( !empty($insertBeforeTagName) ) {
            $domNode= $document->getElementsByTagName( $insertBeforeTagName )->item(0);
            self::insertSignature($key, array ( $publicCertificate ), $element, $domNode);
        } else {
            self::insertSignature($key, array ( $publicCertificate ), $element);
        }
        $requestXML = $element->ownerDocument->saveXML($element);
        if(MSI_DEBUG) MoIDPUtility::mo_debug("Logout Response Generated:" .  $requestXML);
        return $requestXML;
    }

	public static function getEncryptionAlgorithm($method)
    {
		switch($method)
        {
			case 'http://www.w3.org/2001/04/xmlenc#tripledes-cbc':
				return XMLSecurityKey::TRIPLEDES_CBC;
				break;

			case 'http://www.w3.org/2001/04/xmlenc#aes128-cbc':
				return XMLSecurityKey::AES128_CBC;

			case 'http://www.w3.org/2001/04/xmlenc#aes192-cbc':
				return XMLSecurityKey::AES192_CBC;
				break;

			case 'http://www.w3.org/2001/04/xmlenc#aes256-cbc':
				return XMLSecurityKey::AES256_CBC;
				break;

			case 'http://www.w3.org/2001/04/xmlenc#rsa-1_5':
				return XMLSecurityKey::RSA_1_5;
				break;

			case 'http://www.w3.org/2001/04/xmlenc#rsa-oaep-mgf1p':
				return XMLSecurityKey::RSA_OAEP_MGF1P;
				break;

			case 'http://www.w3.org/2000/09/xmldsig#dsa-sha1':
				return XMLSecurityKey::DSA_SHA1;
				break;

			case 'http://www.w3.org/2000/09/xmldsig#rsa-sha1':
				return XMLSecurityKey::RSA_SHA1;
				break;

			case 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256':
				return XMLSecurityKey::RSA_SHA256;
				break;

			case 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha384':
				return XMLSecurityKey::RSA_SHA384;
				break;

			case 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha512':
				return XMLSecurityKey::RSA_SHA512;
				break;

			default:
				echo sprintf('Invalid Encryption Method: '.$method);
				exit;
				break;
		}
	}

	public static function sanitize_certificate( $certificate ) {
		$certificate = preg_replace("/[\r\n]+/", "", $certificate);
		$certificate = str_replace( "-", "", $certificate );
		$certificate = str_replace( "BEGIN CERTIFICATE", "", $certificate );
		$certificate = str_replace( "END CERTIFICATE", "", $certificate );
		$certificate = str_replace( " ", "", $certificate );
		$certificate = chunk_split($certificate, 64, "\r\n");
		$certificate = "-----BEGIN CERTIFICATE-----\r\n" . $certificate . "-----END CERTIFICATE-----";
		return $certificate;
	}

	public static function desanitize_certificate( $certificate ) {
		$certificate = preg_replace("/[\r\n]+/", "", $certificate);
				$certificate = str_replace( "-----BEGIN CERTIFICATE-----", "", $certificate );
		$certificate = str_replace( "-----END CERTIFICATE-----", "", $certificate );
		$certificate = str_replace( " ", "", $certificate );
						return $certificate;
	}
}