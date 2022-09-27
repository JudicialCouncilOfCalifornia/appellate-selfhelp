<?php
	//start
	echo '<div class="mo-idp-divided-layout mo-idp-full mo-idp-bg mo-idp-margin-left mo-idp-pt">
            <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width">
                <h2 class="mo-idp-add-new-sp">    
                    Attribute Mapping (Optional)
                    </h2><hr class="mo-idp-add-new-sp-hr">
                    <form name="f" method="post" class="mo-idp-mt-5">
                    <input type="hidden" name="option" value="change_name_id" />
                    <input type="hidden" name="error_message" id="error_message" />';
                echo '  <input type="hidden" name="service_provider" value="'. (isset($sp) && !empty($sp) ? esc_attr($sp->id) : '') .'"/>';
                echo '   <table class="mo-idp-settings-table" style="border-spacing: 0 6px;">
                        <tr id="nameIdTable">
                            <td><strong class="mo-idp-home-card-link">NameID Attribute:</strong></td>
                            <td>';
                                get_nameid_select_box($disabled,$sp);

	echo'					
                            </td>
                            <td>
                                <input  type="submit" 
                                        name="submit" 
                                        style="margin-left:1.25rem;width:6.25rem !important;" 
                                        value="Save" 
                                        class="button button-primary button-large mo-idp-button-large"/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="1" class="mo-idp-note-endp">
                            <b>
                                <span class="mo-idp-red">NOTE: </span>
                                This attribute value is sent in SAML Response. 
                                Users in your Service Provider will be searched (existing users) or created 
                                (new users) based on this attribute. Use <span style="background: #ffc2c2;padding: 1px;">user_email</span> by default.
                            </b>
                            </td>
                        </tr>
                    </table>
                </form>
             </div>';
            echo ' <a href="'.esc_url($license_url).'" class="mo-idp-advt mo-idp-upload-data-anchor">';
            echo' <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width mo-idp-mt-5 mo-idp-border mo-idp-text-color" style="border:3px solid #F2BE54;margin-top: 4rem !important;position:relative;">
             <h2 class="mo-idp-advt-premium-feature-head-jwt-prem mo-idp-flex" style="margin-top:3rem;">Premium Features</h2>';
            echo' <img class="mo-idp-advt-premium-feature-lock-attr-map" src="'.MSI_LOCK.'"/>';
            echo' <div class="mo-idp-flex" style="justify-content:space-around!important;">
             <div class="mo-idp-attr-map-prem-feature  mo-idp-border">
                    <br/>
                    <div class="mo-premium-option">
                        <form name="f" method="post" id="mo_idp_attr_settings">
                            <input type="hidden" name="option" value="mo_idp_attr_settings" />';
                        echo'<input type="hidden" name="service_provider" value="'. (isset($sp) && !empty($sp) ? esc_attr($sp->id) : '') .'"/>';
                        echo'    <table style="border-spacing:0.2rem"> 
                                <tr>
                                    <td style="width:15.625rem" class="mo-idp-home-card-link"><strong>User Attributes</strong></td>
                                </tr>
                                <tr></tr>
                            </table>
                        </form>
                        <div class="mo-idp-mt-5">
                            <b>
                                <span class="mo-idp-red">NOTE:</span>
                                    These are user attributes that will be sent in the SAML Response. 
                                    Choose the User data you want to send in the Response from the dropdown. 
                                    In the textbox to the left of the dropdown, give an appropriate name you 
                                    want the User data mapped to.
                            </b>
                        </div>
                    </div>
                </div>
            <div class="mo-idp-attr-map-prem-feature mo-idp-border"> ';
	//custom attributes
      echo '    <br/>
                <div class="mo-premium-option">
                    <form name="f" method="post" id="mo_custom_idp_attr_settings">
                        <input type="hidden" name="option" value="mo_save_custom_idp_attr" />	
                        <input type="hidden" name="error_message" id="error_message" />';
                    echo'   <input type="hidden" name="service_provider" value="'. (isset($sp) && !empty($sp) ? esc_attr($sp->id) : '') .'"/>';
                    echo'    <table style="border-spacing:0.2rem">
                            <tr>
                                <td class="mo-idp-home-card-link"><strong>Custom Attributes</strong></td>
                            </tr>
                        </table>
			        </form>
                    <div class="mo-idp-mt-5">
                        <b>
                            <span class="mo-idp-red">NOTE:</span> These are extra static attributes that will be sent in the SAML Response. 
                            Enter the data you want to send in the Response from the dropdown. 
                            In the textbox to the left of the dropdown, give an appropriate name you want the data mapped to.
                        </b>
                    </div>
                </div>
            </div>
		    <div class="mo-idp-attr-map-prem-feature mo-idp-border">';
                    $role_mapping_enabled = false;
                    $role_mapping_hidden  = "hidden";
	//Group Mapping
	echo '	    <br/>
                <div class="mo-premium-option">
                    <div class="mo-idp-home-card-link"><b>Group/Role Mapping</b></div>
                    <br>
                    <form name="f" method="post" id="mo_idp_group_setting">
                        <input type="hidden" name="option" value="mo_add_role_attribute" />
                        <input type="hidden" name="error_message" id="error_message" />';
                    echo' <input type="hidden" name="service_provider" value="'. (isset($sp) && !empty($sp) ? esc_attr($sp->id) : '') .'"/>';
                    echo'   <div class="mo-idp-mt-5">';
                    echo'       <input  type="checkbox" 
                                    class="mo_idp_checkbox" disabled 
                                    name="idp_role_attribute" 
                                    value="1" '. esc_attr($role_mapping_enabled);
         		                (empty($sp) ? 'disabled title="Disabled. Configure your Identity Provider"' : '') .' /> ';
                    echo'       Check this option if you want to send User Roles as Group Attribute';
                    echo'       <div id="idp_role_attr_name" class="mo-idp-help-desc" '. esc_attr($role_mapping_hidden) .'>';
                    echo'            <input  type="text" style="margin-bottom:1%;" disabled 
                                        name="mo_idp_role_mapping_name" 
                                        placeholder="Name" 
                                        value="'. (isset($sp_user_attr_result['groupMapName']) ? esc_attr($sp_user_attr_result['groupMapName']) : '') .'" />';	
                    echo '           <b>
                                    <span style="margin-left:2%;" class="mo-idp-red">NOTE:</span> User Role will be mapped to this name in the SAML Response
                                </b>
                            </div>
                        </div>
                    </form>
                </div>
                </div>
            </div>
        </div>
        </a>';
    
