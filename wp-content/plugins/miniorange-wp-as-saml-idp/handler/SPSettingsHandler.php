<?php

namespace IDP\Handler;

use IDP\Helper\Constants\MoIDPMessages;
use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\MoIDPUtility;
use IDP\Helper\Utilities\SAMLUtilities;

final class SPSettingsHandler extends SPSettingsUtility
{
    use Instance;

    
    private function __construct(){}

    
    public function _mo_idp_save_new_sp($POSTED)
	{
        
		global $dbIDPQueries;

		$this->checkIfValidPlugin();
		$this->checkIfRequiredFieldsEmpty(array('idp_sp_name'=>$POSTED,'idp_sp_issuer'=>$POSTED,
												'idp_acs_url'=>$POSTED,'idp_nameid_format'=>$POSTED));

		$where = $data = array();
		$check = $where['mo_idp_sp_name'] = $data['mo_idp_sp_name'] = sanitize_text_field($POSTED['idp_sp_name']);
		$issuer = $data['mo_idp_sp_issuer'] = sanitize_text_field($POSTED['idp_sp_issuer']);

		$this->checkIssuerAlreadyInUse($issuer,NULL,$check);
		$this->checkNameALreadyInUse($check);

		$data = $this->collectData($POSTED,$data);

		$insert = $dbIDPQueries->insert_sp_data($data);

		do_action('mo_idp_show_message',MoIDPMessages::showMessage('SETTINGS_SAVED'),'SUCCESS');
	}

    
    public function _mo_idp_edit_sp($POSTED)
	{
        
		global $dbIDPQueries;

		$this->checkIfValidPlugin();
		$this->checkIfRequiredFieldsEmpty(array('idp_sp_name'=>$POSTED,'idp_sp_issuer'=>$POSTED,
												'idp_acs_url'=>$POSTED,'idp_nameid_format'=>$POSTED));
		$this->checkIfValidServiceProvider($POSTED,TRUE,'service_provider');

		$data 		= $where 					= array();
		$id 		= $where['id'] 				= $POSTED['service_provider'];
		$check 		= $data['mo_idp_sp_name'] 	= sanitize_text_field( $POSTED['idp_sp_name']	);
		$issuer 	= $data['mo_idp_sp_issuer'] = sanitize_text_field( $POSTED['idp_sp_issuer']	);

		$this->checkIfValidServiceProvider($dbIDPQueries->get_sp_data($id));
		$this->checkIssuerAlreadyInUse($issuer,$id,NULL);
		$this->checkNameALreadyInUse($check,$id);

		$data = $this->collectData($POSTED,$data);

		$dbIDPQueries->update_sp_data($data,$where);

		do_action('mo_idp_show_message',MoIDPMessages::showMessage('SETTINGS_SAVED'),'SUCCESS');
	}

	public function mo_idp_delete_sp_settings($POSTED)
	{
        
		global $dbIDPQueries;

		MoIDPUtility::startSession();
		$this->checkIfValidPlugin();

		$spWhere 					= array();
		$spWhere['id'] 				= $POSTED['sp_id'];
		$spAttrWhere['mo_sp_id'] 	= $POSTED['sp_id'];

		$dbIDPQueries->delete_sp($spWhere,$spAttrWhere);

		if(array_key_exists('SP',$_SESSION)) unset($_SESSION['SP']);

		do_action('mo_idp_show_message',MoIDPMessages::showMessage('SP_DELETED'),'SUCCESS');
	}

    
    public function mo_idp_change_name_id($POSTED)
	{
        
		global $dbIDPQueries;

		$this->checkIfValidPlugin();
		$this->checkIfValidServiceProvider($POSTED,TRUE,'service_provider');

		$data 						= $where 		= array();
		$sp_id 						= $where['id'] 	= $POSTED['service_provider'];
		$data['mo_idp_nameid_attr'] = $POSTED['idp_nameid_attr'];
		$dbIDPQueries->update_sp_data($data,$where);
		do_action('mo_idp_show_message',MoIDPMessages::showMessage('SETTINGS_SAVED'),'SUCCESS');
	}

    
    public function _mo_sp_change_settings($POSTED)
	{
		$this->checkIfValidPlugin();
		$this->checkIfValidServiceProvider($POSTED,TRUE,'service_provider');
	}

    
    private function collectData($POSTED, $data)
	{
		$data['mo_idp_acs_url'] 			= sanitize_text_field($POSTED['idp_acs_url']);
		$data['mo_idp_nameid_format'] 		= sanitize_text_field($POSTED['idp_nameid_format']);
		$data['mo_idp_protocol_type']		= sanitize_text_field($POSTED['mo_idp_protocol_type']);

		$data['mo_idp_logout_url'] 			= NULL;
		$data['mo_idp_cert'] 				= !empty($POSTED['mo_idp_cert']) 				? SAMLUtilities::sanitize_certificate(trim($POSTED['mo_idp_cert'])) 		: NULL;
		$data['mo_idp_cert_encrypt'] 		= NULL;
		$data['mo_idp_default_relayState'] 	= !empty($POSTED['idp_default_relayState']) 	? sanitize_text_field($POSTED['idp_default_relayState']) 					: NULL;
		$data['mo_idp_logout_binding_type'] = !empty($POSTED['mo_idp_logout_binding_type']) ? $POSTED['mo_idp_logout_binding_type'] 									: 'HttpRedirect';

		$data['mo_idp_response_signed'] 	= NULL;
		$data['mo_idp_assertion_signed'] 	= isset($POSTED['idp_assertion_signed']) 		? $POSTED['idp_assertion_signed'] 		: NULL;
		$data['mo_idp_encrypted_assertion'] = NULL;

		$this->checkIfValidEncryptionCertProvided($data['mo_idp_encrypted_assertion'],$data['mo_idp_cert_encrypt']);

		return $data;
	}
}