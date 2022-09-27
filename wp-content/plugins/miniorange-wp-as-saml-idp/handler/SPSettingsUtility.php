<?php

namespace IDP\Handler;

use IDP\Exception\InvalidEncryptionCertException;
use IDP\Exception\IssuerValueAlreadyInUseException;
use IDP\Exception\NoServiceProviderConfiguredException;
use IDP\Exception\SPNameAlreadyInUseException;
use IDP\Helper\Database\MoDbQueries;
use IDP\Helper\Utilities\MoIDPUtility;

class SPSettingsUtility extends BaseHandler
{

    /**
     * @param $sp
     * @param bool $isArray
     * @param null $key
     * @throws NoServiceProviderConfiguredException
     */
    public function checkIfValidServiceProvider($sp, $isArray=FALSE, $key=NULL)
	{
		if(($isArray && array_key_exists($key,$sp) && MoIDPUtility::isBlank($sp[$key]))
			|| MoIDPUtility::isBlank($sp)) throw new NoServiceProviderConfiguredException();
	}

    /**
     * @param $issuer
     * @param $id
     * @param $name
     * @throws IssuerValueAlreadyInUseException
     */
    public function checkIssuerAlreadyInUse($issuer, $id, $name)
	{
	    /** @global MoDbQueries $dbIDPQueries */
		global $dbIDPQueries;
		$sp = $dbIDPQueries->get_sp_from_issuer($issuer);

		if(!MoIDPUtility::isBlank($sp) && !MoIDPUtility::isBlank($id)
			&& $sp->id!=$id) throw new IssuerValueAlreadyInUseException($sp);

		if(!MoIDPUtility::isBlank($sp) && !MoIDPUtility::isBlank($name)
			&& $name != $sp->mo_idp_sp_name) throw new IssuerValueAlreadyInUseException($sp);
	}

    /**
     * @param $name
     * @param null $id
     * @throws SPNameAlreadyInUseException
     */
    public function checkNameALreadyInUse($name, $id=NULL)
	{
        /** @global MoDbQueries $dbIDPQueries */
		global $dbIDPQueries;
		$sp = $dbIDPQueries->get_sp_from_name($name);

		if(!MoIDPUtility::isBlank($sp) && !MoIDPUtility::isBlank($id)
			&& $sp->id!=$id) throw new SPNameAlreadyInUseException($sp);

		if(!MoIDPUtility::isBlank($sp) && MoIDPUtility::isBlank($id))
			throw new SPNameAlreadyInUseException($sp);
	}

	public function checkIfValidEncryptionCertProvided($option,$cert)
	{
		if(!MoIDPUtility::isBlank($option) && MoIDPUtility::isBlank($cert)) throw new InvalidEncryptionCertException();
	}

}