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
use IDP\Exception\MetadataFileException; 
use IDP\Exception\RequiredSpNameException; 
use IDP\Handler\FeedbackHandler; 
use IDP\Handler\IDPSettingsHandler; 
use IDP\Handler\SPSettingsHandler; 
use IDP\Handler\SupportHandler; 
use IDP\Helper\Traits\Instance; 
use IDP\Helper\Utilities\MoIDPUtility; 
use IDP\Handler\DemoRequestHandler; 
 
class SettingsActions extends BasePostAction 
{ 
    use Instance; 
 
    /** @var SPSettingsHandler $handler */ 
    private $handler; 
    /** @var SupportHandler $supportHandler */ 
    private $supportHandler; 
    /** @var IDPSettingsHandler $idpSettingsHandler */ 
    private $idpSettingsHandler; 
    /** @var FeedbackHandler $feedbackHandler */ 
    private $feedbackHandler; 
    /** @var DemoRequestHandler $demoRequestHandler */ 
	private $demoRequestHandler; 
 
    public function __construct() 
    { 
		$this->handler = SPSettingsHandler::instance(); 
		$this->supportHandler = SupportHandler::instance(); 
		$this->idpSettingsHandler = IDPSettingsHandler::instance(); 
		$this->feedbackHandler = FeedbackHandler::instance(); 
        $this->demoRequestHandler = DemoRequestHandler::instance(); 
		$this->_nonce = 'idp_settings'; 
		parent::__construct(); 
    } 
 
    private $funcs = array( 
		'mo_add_idp', 
        'mo_edit_idp', 
        'mo_show_sp_settings', 
        'mo_idp_delete_sp_settings', 
        'mo_idp_entity_id', 
        'change_name_id', 
        'mo_idp_contact_us_query_option', 
        'mo_idp_feedback_option', 
        'mo_idp_use_new_cert', 
        'saml_idp_upload_metadata', 
        'mo_idp_request_demo' 
	); 
 
	public function handle_post_data() 
	{ 
		if ( current_user_can( 'manage_options' ) and isset( $_POST['option'] ) ) 
		{
			$option = trim( sanitize_text_field( $_POST['option'] ) ); 
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
			}catch (MetadataFileException $e){ 
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR'); 
			}catch (RequiredSpNameException $e){ 
				do_action('mo_idp_show_message',$e->getMessage(),'ERROR'); 
			}catch (\Exception $e){ 
				if(MSI_DEBUG) MoIDPUtility::mo_debug("Exception Occurred during SSO " . $e); 
				wp_die($e->getMessage()); 
			} 
		} 
	} 
 
    /** 
     * @param $option 
     * @throws InvalidEncryptionCertException 
     * @throws IssuerValueAlreadyInUseException 
     * @throws NoServiceProviderConfiguredException 
     * @throws RequiredFieldsException 
     * @throws SPNameAlreadyInUseException 
     * @throws \IDP\Exception\SupportQueryRequiredFieldsException 
     * @throws \IDP\Exception\MetadataFileException 
     * @throws \IDP\Exception\RequiredSpNameException 
     */ 
    public function route_post_data($option) 
    { 
        $POSTED = MoIDPUtility::sanitizeAssociateArray($_POST);
        switch ($option) { 
            case $this->funcs[0]: 
                $this->handler->_mo_idp_save_new_sp($POSTED); 
                break; 
            case $this->funcs[1]: 
                $this->handler->_mo_idp_edit_sp($POSTED); 
                break; 
            case $this->funcs[2]: 
                $this->handler->_mo_sp_change_settings($POSTED); 
                break; 
            case $this->funcs[3]: 
                $this->handler->mo_idp_delete_sp_settings($POSTED); 
                break; 
            case $this->funcs[4]: 
                $this->idpSettingsHandler->mo_change_idp_entity_id($POSTED); 
                break; 
            case $this->funcs[5]: 
                $this->handler->mo_idp_change_name_id($POSTED); 
                break; 
            case $this->funcs[6]: 
                $this->supportHandler->_mo_idp_support_query($POSTED); 
                break; 
            case $this->funcs[7]: 
                $this->feedbackHandler->_mo_send_feedback($POSTED); 
                break; 
            case $this->funcs[8]: 
                MoIDPUtility::useNewCerts(); 
                break; 
            case $this->funcs[9]:
                $this->handler->_mo_idp_metadata_new_sp($POSTED); 
                break; 
            case $this->funcs[10]: 
                $this->demoRequestHandler->mo_idp_demo_Request_function($POSTED);	 
                break;	 
        } 
    } 
 
	public function changeSPInSession($POSTED) 
    { 
        MoIDPUtility::startSession(); 
        $_SESSION['SP'] = array_key_exists('service_provider', $POSTED) && 
        !MoIDPUtility::isBlank($POSTED['service_provider']) ? sanitize_text_field($POSTED['service_provider']) : 1; 
    } 
}