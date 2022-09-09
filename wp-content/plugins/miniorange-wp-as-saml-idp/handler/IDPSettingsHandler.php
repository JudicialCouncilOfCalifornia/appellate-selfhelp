<?php

namespace IDP\Handler;

use IDP\Helper\Constants\MoIDPMessages;
use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\MoIDPUtility;

final class IDPSettingsHandler extends BaseHandler
{
    use Instance;

    
    private function __construct(){}

    public function mo_change_idp_entity_id($POSTED)
	{
		$this->checkIfValidPlugin();

		if(array_key_exists('mo_saml_idp_entity_id', $POSTED) && !empty($POSTED['mo_saml_idp_entity_id']))
		{
			update_site_option('mo_idp_entity_id',$POSTED['mo_saml_idp_entity_id']);
			MoIDPUtility::createMetadataFile();			do_action('mo_idp_show_message',MoIDPMessages::showMessage('IDP_ENTITY_ID_CHANGED'),'SUCCESS');
		}else
			do_action('mo_idp_show_message',MoIDPMessages::showMessage('IDP_ENTITY_ID_NULL'),'ERROR');

	}
}