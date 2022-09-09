<?php

namespace IDP\Actions;

use IDP\Exception\InvalidEncryptionCertException;
use IDP\Exception\InvalidOperationException;
use IDP\Exception\IssuerValueAlreadyInUseException;
use IDP\Exception\JSErrorException;
use IDP\Exception\NoServiceProviderConfiguredException;
use IDP\Exception\NotRegisteredException;
use IDP\Exception\RequiredFieldsException;
use IDP\Exception\SPNameAlreadyInUseException;
use IDP\Handler\FeedbackHandler;
use IDP\Handler\IDPSettingsHandler;
use IDP\Handler\SPSettingsHandler;
use IDP\Handler\SupportHandler;
use IDP\Handler\VisualTourHandler;
use IDP\Helper\Traits\Instance;
use IDP\Helper\Utilities\MoIDPUtility;

class SettingsActions extends BasePostAction
{
    use Instance;

    
    private $handler;
    
    private $supportHandler;
    
    private $idpSettingsHandler;
    
    private $feedbackHandler;
    
    private $visualTourHandler;

    public function __construct()
    {
        $this->handler = SPSettingsHandler::instance();
        $this->supportHandler = SupportHandler::instance();
        $this->idpSettingsHandler = IDPSettingsHandler::instance();
        $this->feedbackHandler = FeedbackHandler::instance();
        $this->visualTourHandler = VisualTourHandler::instance();
        $this->_nonce = 'idp_settings';
        parent::__construct();
    }

    private $funcs = array (
		'mo_add_idp',
		'mo_edit_idp',
		'mo_show_sp_settings',
		'mo_idp_delete_sp_settings',
		'mo_idp_entity_id',
		'change_name_id',
		'mo_idp_contact_us_query_option',
        'mo_idp_feedback_option',
        'clear_pointers',
	);

	public function handle_post_data()
	{
		if ( current_user_can( 'manage_options' ) and isset( $_POST['option'] ) )
		{
			$option = trim($_POST['option']);
			try{
				$this->route_post_data($option);
				$this->changeSPInSession($_POST);
			}catch (NotRegisteredException $e) {
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (NoServiceProviderConfiguredException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (JSErrorException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (RequiredFieldsException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (SPNameAlreadyInUseException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (IssuerValueAlreadyInUseException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (InvalidEncryptionCertException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (InvalidOperationException $e){
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR');
			}catch (\Exception $e){
				if(MSI_DEBUG) MoIDPUtility::mo_debug("Exception Occurred during SSO " . $e);
				wp_die($e->getMessage());
			}
		}
	}

    
    public function route_post_data($option)
	{

		switch($option)
		{
			case $this->funcs[0]:
				$this->handler->_mo_idp_save_new_sp($_POST);					    break;
			case $this->funcs[1]:
				$this->handler->_mo_idp_edit_sp($_POST);						    break;
			case $this->funcs[2]:
				$this->handler->_mo_sp_change_settings($_POST);				        break;
			case $this->funcs[3]:
				$this->handler->mo_idp_delete_sp_settings($_POST);			        break;
			case $this->funcs[4]:
				$this->idpSettingsHandler->mo_change_idp_entity_id($_POST);		    break;
			case $this->funcs[5]:
				$this->handler->mo_idp_change_name_id($_POST);				        break;
			case $this->funcs[6]:
				$this->supportHandler->_mo_idp_support_query($_POST);				break;
            case $this->funcs[7]:
				$this->feedbackHandler->_mo_send_feedback();    				    break;
            case $this->funcs[8]:
                $this->visualTourHandler->_mo_restart_tour($_POST);				    break;
		}
	}

	public function changeSPInSession($POSTED)
	{
		MoIDPUtility::startSession();
		$_SESSION['SP'] = array_key_exists('service_provider', $POSTED) &&
							!MoIDPUtility::isBlank($POSTED['service_provider']) ? $POSTED['service_provider'] : 1;
	}
}