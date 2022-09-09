<?php

namespace IDP\Helper\WSFED;

use IDP\Helper\Constants\MoIDPConstants;
use IDP\Helper\Utilities\MoIDPUtility;
use IDP\Helper\Factory\RequestHandlerFactory;
use IDP\Exception\InvalidRequestInstantException;
use IDP\Exception\InvalidRequestVersionException;
use IDP\Exception\MissingIssuerValueException;

class WsFedRequest implements RequestHandlerFactory
{   

    private $clientRequestId;
    private $username;
    private $wreply;
    private $wres;
    private $wctx;
    private $wp;
    private $wct;
    private $wfed;
    private $wencoding;
    private $wfresh;
    private $wauth;
    private $wreq;
    private $whr;
    private $wreqptr;

    private $wa;
    private $wtrealm;
    private $requestType =  MoIDPConstants::WS_FED;

    public function __construct($REQUEST)
    {
		$this->clientRequestId 	= array_key_exists('client-request-id', $REQUEST) ? $REQUEST['client-request-id'] : NULL;
		$this->username  		= array_key_exists('username', $REQUEST) 		  ? $REQUEST['username'] : NULL;
		$this->wa  			 	= array_key_exists('wa', $REQUEST) 				  ? $REQUEST['wa'] : NULL;
		$this->wtrealm  		= array_key_exists('wtrealm', $REQUEST) 		  ? $REQUEST['wtrealm'] : NULL;
		$this->wctx  			= array_key_exists('wctx', $REQUEST) 			  ? $REQUEST['wctx'] : NULL;
		$this->wct 			 	= array_key_exists('wctx', $REQUEST) 			  ? $REQUEST['wctx'] : NULL;

		if(MoIDPUtility::isBlank($this->wa)) throw new MissingWaAttributeException();
		if(MoIDPUtility::isBlank($this->wtrealm)) throw new MissingWtRealmAttributeException();
    }

    public function generateRequest()
    {
        return;
    }

   	public function __toString()
    {
        $html = 'WS-FED REQUEST PARAMS [';
        $html .= ' wa = '.$this->wa;
        $html .= ', wtrealm =  '.$this->wtrealm;
       	$html .= ', clientRequestId = '. $this->clientRequestId;
	    $html .= ', username = '. $this->username;
	    $html .= ', wreply = '. $this->wreply;
	    $html .= ', wres = '. $this->wres;
	    $html .= ', wctx = '. $this->wctx;
	    $html .= ', wp = '. $this->wp;
	    $html .= ', wct = '. $this->wct;
	    $html .= ', wfed = '. $this->wfed;
	    $html .= ', wencoding = '. $this->wencoding;
	    $html .= ', wfresh = '. $this->wfresh;
	    $html .= ', wauth = '. $this->wauth;
	    $html .= ', wreq = '. $this->wreq;
	    $html .= ', whr = '. $this->whr;
	    $html .= ', wreqptr = '. $this->wreqptr;
        $html .= ']';
        return $html;
    }

    public function getClientRequestId()
    {
        return $this->clientRequestId;
    }

    public function setClientRequestId($clientRequestId)
    {
        $this->clientRequestId = $clientRequestId;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getWreply()
    {
        return $this->wreply;
    }

    public function setWreply($wreply)
    {
        $this->wreply = $wreply;

        return $this;
    }

    public function getWres()
    {
        return $this->wres;
    }

    public function setWres($wres)
    {
        $this->wres = $wres;

        return $this;
    }

    public function getWctx()
    {
        return $this->wctx;
    }

    public function setWctx($wctx)
    {
        $this->wctx = $wctx;

        return $this;
    }

    public function getWp()
    {
        return $this->wp;
    }

    public function setWp($wp)
    {
        $this->wp = $wp;

        return $this;
    }

    public function getWct()
    {
        return $this->wct;
    }

    public function setWct($wct)
    {
        $this->wct = $wct;

        return $this;
    }

    public function getWfed()
    {
        return $this->wfed;
    }

    public function setWfed($wfed)
    {
        $this->wfed = $wfed;

        return $this;
    }

    public function getWencoding()
    {
        return $this->wencoding;
    }

    public function setWencoding($wencoding)
    {
        $this->wencoding = $wencoding;

        return $this;
    }

    public function getWfresh()
    {
        return $this->wfresh;
    }

    public function setWfresh($wfresh)
    {
        $this->wfresh = $wfresh;

        return $this;
    }

    public function getWauth()
    {
        return $this->wauth;
    }

    public function setWauth($wauth)
    {
        $this->wauth = $wauth;

        return $this;
    }

    public function getWreq()
    {
        return $this->wreq;
    }

    public function setWreq($wreq)
    {
        $this->wreq = $wreq;

        return $this;
    }

    public function getWhr()
    {
        return $this->whr;
    }

    public function setWhr($whr)
    {
        $this->whr = $whr;

        return $this;
    }

    public function getWreqptr()
    {
        return $this->wreqptr;
    }

    public function setWreqptr($wreqptr)
    {
        $this->wreqptr = $wreqptr;

        return $this;
    }

    public function getWa()
    {
        return $this->wa;
    }

    public function setWa($wa)
    {
        $this->wa = $wa;

        return $this;
    }

    public function getWtrealm()
    {
        return $this->wtrealm;
    }

    public function setWtrealm($wtrealm)
    {
        $this->wtrealm = $wtrealm;

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