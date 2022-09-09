<?php
		echo "<div class='mo_idp_divided_layout mo-idp-full'>
            <div class='mo_idp_table_layout mo-idp-center'>
                <h2>    
                    ATTRIBUTE MAPPING (OPTIONAL)";
                    restart_tour();
    echo        "</h2><hr>";
		echo '      
                <form name="f" method="post" id="mo_name_idp_attr_settings">
                    <input type="hidden" name="option" value="change_name_id" />
                    <input type="hidden" name="error_message" id="error_message" />
                    <input type="hidden" name="service_provider" value="'. (isset($sp) && !empty($sp) ? $sp->id : '') .'"/>
    
                    <table class="mo_idp_settings_table">
                        <tr id="nameIdTable" style="background-color: white">
                            <td style="width:150px;"><strong>NameID Attribute:</strong></td>
                            <td>';

                                get_nameid_select_box($disabled,$sp);

	echo'					
                            </td>
                            <td>
                                <input  type="submit" 
                                        name="submit" 
                                        style="margin-left:20px;width:100px;" 
                                        value="Save" 
                                        class="button button-primary button-large"/>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="2">
                                <i>
                                    <span style="color:red">NOTE: </span>
                                    This attribute value is sent in SAML Response. 
                                    Users in your Service Provider will be searched (existing users) or created 
                                    (new users) based on this attribute. Use EmailAddress by default.
                                </i>
                            </td>
                        </tr>
                    </table>
                </form>
             </div>
		     <div class="mo_idp_table_layout mo-idp-center">
                <br/>
                <div class="mo_idp_premium_option_text">
                    <span style="color:red;">*</span> This is a premium feature. Check 
                    <a href="'.$license_url.'">Licensing Tab</a> to learn more.
                </div>
                <div class="mo_premium_option">
                    <form name="f" method="post" id="mo_idp_attr_settings">
                        <input type="hidden" name="option" value="mo_idp_attr_settings" />
                        <input type="hidden" name="service_provider" value="'. (isset($sp) && !empty($sp) ? $sp->id : '') .'"/>
                        <table class="mo_idp_settings_table">
                            <tr>
                                <td style="width:250px"><strong>User Attributes (OPTIONAL):</strong></td>
                                <td>
                                    <input  type="button" 
                                            name="add_attribute" 
                                            value="+" disabled 
                                            onclick="add_user_attribute();" 
                                            class="button button-primary" />&nbsp;
                                    <input  type="button" 
                                            name="remove_attribute" 
                                            value="-" disabled 
                                            onclick="remove_user_attribute();" 
                                            class="button button-primary" />
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Name</strong></td>
                                <td><strong>User Meta Data</strong></td>
                            </tr>';

	echo 		'	    </table>
			        </form>
                    <i>
                        <span style="color:red;">NOTE:</span>
                            These are user attributes that will be send in the SAML Response. 
                            Choose the User data you want to send in the Response from the dropdown. 
                            In the textbox to the left of the dropdown give an appropriate name you 
                            want the User data mapped to.
                    </i>
                </div>
            </div>
            <div class="mo_idp_table_layout mo-idp-center">';

	        echo 	'<br/>
                <div class="mo_idp_premium_option_text">
                    <span style="color:red;">*</span> This is a premium feature. Check 
                    <a href="'.$license_url.'">Licensing Tab</a> to learn more.
                </div>
                <div class="mo_premium_option">
                    <form name="f" method="post" id="mo_custom_idp_attr_settings">
                        <input type="hidden" name="option" value="mo_save_custom_idp_attr" />	
                        <input type="hidden" name="error_message" id="error_message" />
                        <input type="hidden" name="service_provider" value="'. (isset($sp) && !empty($sp) ? $sp->id : '') .'"/>
                        <table class="mo_idp_settings_table">
                            <tr>
                                <td><strong>Custom Attributes (OPTIONAL):</strong></td>
                                <td>
                                    <input  type="button" 
                                            name="add_custom_attribute" 
                                            value="+"  
                                            onclick="add_custom_attributes();" disabled 
                                            class="button button-primary" />&nbsp;
                                    <input  type="button" 
                                            name="remove_custom_attribute" 
                                            value="-" disabled 
                                            onclick="remove_custom_attributes();" 
                                            class="button button-primary" />
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Name</strong></td>
                                <td><strong>Custom Attribute Value</strong></td>
                            </tr>';

        echo		'    </table>
			        </form>
                    <i>
                        <span style="color:red;">NOTE:</span>These are extra static attributes that will be send in the SAML Response. 
                        Enter the data you want to send in the Response from the dropdown. 
                        In the textbox to the left of the dropdown give an appropriate name you want the data mapped to.
                    </i>
                </div>
            </div>
		    <div class="mo_idp_table_layout mo-idp-center">';

                    $role_mapping_enabled = false;
                    $role_mapping_hidden  = "hidden";

		echo '	    <br/>
	            <div class="mo_idp_premium_option_text">
                    <span style="color:red;">*</span> This is a premium feature. Check 
                    <a href="'.$license_url.'">Licensing Tab</a> to learn more.
                </div>
                <div class="mo_premium_option">
                    <div><h3>Group/Role Mapping (Optional)</h3></div>
                    <form name="f" method="post" id="mo_idp_group_setting">
                        <input type="hidden" name="option" value="mo_add_role_attribute" />
                        <input type="hidden" name="error_message" id="error_message" />
                        <input type="hidden" name="service_provider" value="'. (isset($sp) && !empty($sp) ? $sp->id : '') .'"/>
                        <div>
                            <input  type="checkbox" 
                                    class="mo_idp_checkbox" disabled 
                                    name="idp_role_attribute" 
                                    value="1" '. $role_mapping_enabled;
        echo 		                (empty($sp) ? 'disabled title="Disabled. Configure your Identity Provider"' : '') .' /> 
                            Check this option if you want to send User Roles as Group Attribute
                            
                            <div id="idp_role_attr_name" class="mo_idp_help_desc" '. $role_mapping_hidden .'>
                                <input  type="text" style="margin-bottom:1%;" disabled 
                                        name="mo_idp_role_mapping_name" 
                                        placeholder="Name" 
                                        value="'. (isset($sp_user_attr_result['groupMapName']) ? $sp_user_attr_result['groupMapName'] : '') .'" />	
                                <i>
                                    <span style="margin-left:2%;color:red;">NOTE:</span> User Role will be mapped to this name in the SAML Response
                                </i>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>';
