<?php

namespace IDP\Handler;

use IDP\Helper\Constants\MoIDPConstants;
use IDP\Helper\Factory\ResponseDecisionHandler;
use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\MoIDPUtility;

final class SendResponseHandler extends BaseHandler
{
    use Instance;

    
    private function __construct(){}

    
    public function mo_idp_send_reponse($args, $login = NULL)
	{
		if(MSI_DEBUG) MoIDPUtility::mo_debug("Generating Login Response");
		$this->checkIfValidPlugin();

		$current_user = wp_get_current_user();
		$current_user = !MoIDPUtility::isBlank($current_user->ID) ? $current_user :  get_user_by('login',$login);

		if(strcasecmp($args['requestType'],MoIDPConstants::AUTHN_REQUEST) ==0 )
			$args = $this->getSAMLResponseParams($args,$current_user);
		elseif(strcasecmp($args['requestType'],MoIDPConstants::WS_FED) ==0 )
			$args = $this->getWSFedResponseParams($args,$current_user);

		$response_obj 	= ResponseDecisionHandler::getResponseHandler(
		    $args[0],
            array($args[1],$args[2],$args[3],$args[4],$args[5],$args[6],$login)
        );
		$response 		= $response_obj->generateResponse();

		if(MSI_DEBUG) MoIDPUtility::mo_debug( "Login Response generated: " . $response);

        if (ob_get_contents()) ob_clean();
		MoIDPUtility::unsetCookieVariables(['response_params','moIdpsendSAMLResponse','acs_url','audience','relayState'
            ,'requestID','moIdpsendWsFedResponse','wtrealm','wa','wctx','clientRequestId']
        );

		if(strcasecmp($args[0],MoIDPConstants::SAML_RESPONSE) ==0 )
			$this->_send_response($response, $args[7],$args[1]);
		elseif(strcasecmp($args[0],MoIDPConstants::WS_FED_RESPONSE) ==0 )
			$this->_send_ws_fed_response($response,$args[5]->mo_idp_acs_url."?clientRequestId=".$args[8],$args[3],$args[2]);
	}

	public function getSAMLResponseParams($args,$current_user)
	{
        
		global $dbIDPQueries;
		$acs_url = $args['acs_url'];
		$audience = $args['issuer'];
		$relayState = isset($args['relayState']) ? $args['relayState'] : NULL;
		$requestID = isset($args['requestID']) ? $args['requestID'] : NULL;

		MoIDPUtility::addSPCookie($audience);

		$blogs 		= is_multisite() ? get_sites() : null;
		$site_url 	= is_null($blogs) ? site_url('/') : get_site_url($blogs[0]->blog_id,'/');
		$issuer 	= get_site_option('mo_idp_entity_id') ?  get_site_option('mo_idp_entity_id') : MSI_URL;

		$sp 		= $dbIDPQueries->get_sp_from_acs($acs_url);
		$id 		= !empty($sp) ? $sp->id : null;
		$sp_attr 	= $dbIDPQueries->get_all_sp_attributes($id);

		return array(MoIDPConstants::SAML_RESPONSE,$acs_url,$issuer,$audience,$requestID,$sp_attr,$sp,$relayState,NULL);
	}

	public function getWSFedResponseParams($args,$current_user)
	{
        
		global $dbIDPQueries;

		$clientRequestId = $args['clientRequestId'];
		$wtrealm = $args['wtrealm'];
		$wa = $args['wa'];
		$relayState = isset($args['relayState']) ? $args['relayState'] : NULL;
		$wctx = isset($args['wctx']) ? $args['wctx'] : NULL;

				
		$blogs 		= is_multisite() ? get_sites() : null;
		$site_url 	= is_null($blogs) ? site_url('/') : get_site_url($blogs[0]->blog_id,'/');
		$issuer 	= get_site_option('mo_idp_entity_id') ?  get_site_option('mo_idp_entity_id') : MSI_URL;

		$sp 		= $dbIDPQueries->get_sp_from_issuer($wtrealm);
		$id 		= !empty($sp) ? $sp->id : null;
		$sp_attr 	= $dbIDPQueries->get_all_sp_attributes($id);

		return array(MoIDPConstants::WS_FED_RESPONSE,$wtrealm,$wa,$wctx,$issuer,$sp,$sp_attr,$relayState,$clientRequestId);
	}

	private function _send_response($saml_response, $relayState, $acs_url)
	{
		if(MSI_DEBUG) MoIDPUtility::mo_debug("Sending SAML Login Response");
		$saml_response = base64_encode($saml_response);
        echo'
		<html>
			<head>
				<meta http-equiv="cache-control" content="no-cache">
				<meta http-equiv="pragma" content="no-cache">
			</head>
			<body>
			<form id="responseform" action="'.$acs_url.'" method="post">
				<input type="hidden" name="SAMLResponse" value="'.htmlspecialchars($saml_response).'" />';
        if($relayState!="/") {
            echo '<input type="hidden" name="RelayState" value="' . $relayState . '" />';
        }
        echo '</form>
			</body>
		<script>
			document.getElementById(\'responseform\').submit();	
		</script>
		</html>';
        exit;
	}

	private function _send_ws_fed_response($wsfed_response, $acs_url,$wctx,$wa)
	{
		if(MSI_DEBUG) MoIDPUtility::mo_debug("Sending WS-FED Login Response");
        echo'
		<html>
			<head>
				<meta http-equiv="cache-control" content="no-cache">
				<meta http-equiv="pragma" content="no-cache">
			</head>
			<body>
				<form id="responseform" action="'.$acs_url.'" method="post">
					<input type="hidden" name="wa" value="'.$wa.'" />
			
					<input type="hidden" name="wresult" value="'.htmlentities($wsfed_response).'" />
					<input type="hidden" name="wctx" value="'.$wctx.'" />';
        echo '	</form>
			</body>
			<script>
				document.getElementById(\'responseform\').submit();	
			</script>
		</html>';
        exit;
	}
}