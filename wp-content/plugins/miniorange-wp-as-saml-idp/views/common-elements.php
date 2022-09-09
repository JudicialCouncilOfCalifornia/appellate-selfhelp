<?php

use IDP\Helper\Utilities\MoIDPUtility;
use IDP\Helper\Utilities\TabDetails;
use IDP\Helper\Utilities\Tabs;

function get_user_data_select_box($disabled,$user_info,$sp,$attr=null,$counter=0)
{
	echo '<td><select '.$disabled.' style="width:90%" name="mo_idp_attribute_mapping_val['.$counter.']" ';
	echo (!MoIDPUtility::micr() || !isset($sp))? "disabled":"";
	echo '><option value="">Select User Data to be sent</option>';
	foreach ($user_info as $key => $value) {
		echo '<option value="'.$key.'"';
		if(!is_null($attr))
			echo $attr->mo_sp_attr_value===$key ? 'selected' : '';
		echo '>'.$key.'</option>';
	}
	echo '</tr></td>';
}

function get_nameid_select_box($disabled,$sp)
{
    $user_info = get_user_info_list();
    $nameid = !empty($sp->mo_idp_nameid_attr) && $sp->mo_idp_nameid_attr!='emailAddress' ? $sp->mo_idp_nameid_attr : "user_email";
    if (isset($sp) && !empty($sp)) {
        echo "<select " . $disabled . " style='width:60%'  name='idp_nameid_attr'";
		echo "><option value=''>Select Data to be sent in the NameID</option>";
		foreach ($user_info as $key => $value) {
			echo "<option value='" . $key . "'";
			if (!is_null($sp)) {
				echo $nameid === $key ? "selected" : '';
			}
			echo ">" . $key . "</option>";
		}
		echo "</select>";
    }
    else{
		echo '<div class="mo_idp_note">Please Configure a Service Provider</div>';
	}
}

function get_sp_attr_name_value($sp,$disabled)
{
    
	global $dbIDPQueries;
	$result 				= array();
	$keyunter 				= 0;
	$user_info 				= get_user_info_list();

	if(isset($sp) && !empty($sp))
	{
		$sp_attr = $dbIDPQueries->get_sp_attributes($sp->id);
		$sp_role = $dbIDPQueries->get_sp_role_attribute($sp->id);
		if(isset($sp_attr) && !empty($sp_attr))
		{
			foreach ($sp_attr as $attr) {
				if($attr->mo_sp_attr_name!='groupMapName')
				{
					echo '<tr id="row_'.$keyunter.'">';
					echo '  <td>
                                <input  type="text" '.$disabled.' 
                                        name="mo_idp_attribute_mapping_name['.$keyunter.']" 
                                        placeholder="Name" 
                                        value="'.$attr->mo_sp_attr_name.'"/>
                            </td>';
					get_user_data_select_box($disabled,$user_info,$sp,$attr,$keyunter);
					$keyunter+=1;
				}
			}
		}
		else
		{
			echo '<tr id="row_0"><td><input type="text" name="mo_idp_attribute_mapping_name[0]" placeholder="Name"/></td>';
			get_user_data_select_box($disabled,$user_info,$sp);
		}

		if(isset($sp_role) && !empty($sp_role))
			$result['groupMapName'] = $sp_role->mo_sp_attr_value;
	}
	else{
		echo '<tr id="crow_0">
					<td><div class="mo_idp_note">Please Configure a Service Provider</div></td>
					<td><div class="mo_idp_note">Please Configure a Service Provider</div></td>
				  </tr>';
	}
	$result['user_info'] = $user_info;
	$result['counter']	 = $keyunter;
	return $result;
}

function get_user_info_list()
{
    
	global $dbIDPQueries;
	$current_user = wp_get_current_user();
	    $user_attr = [];
	$user_info = $dbIDPQueries->getDistinctMetaAttributes();
	foreach ($user_info as $key => $value)
		$user_attr[$value->meta_key] = $value->meta_key;
	foreach ($current_user->data as $key => $value)
		$user_attr[$key] = $key;

			$user_attr = apply_filters( 'user_info_attr_list', $user_attr );
	return $user_attr;
}

function check_is_curl_installed()
{
    if(!MoIDPUtility::isCurlInstalled())
    {
        echo'<div id="help_curl_warning_title" class="mo_wpum_title_panel">
			    <p>
			        <font color="#FF0000">
			            Warning: PHP cURL extension is not installed or disabled. 
			            <span style="color:blue">Click here</span> for instructions to enable it.
                    </font>
                </p>
		</div>
		<div hidden="" id="help_curl_warning_desc" class="mo_wpum_help_desc">
			<ul>
				<li>Step 1:&nbsp;&nbsp;&nbsp;&nbsp;Open php.ini file located under php installation folder.</li>
				<li>Step 2:&nbsp;&nbsp;&nbsp;&nbsp;Search for <b>extension=php_curl.dll</b> </li>
				<li>Step 3:&nbsp;&nbsp;&nbsp;&nbsp;Uncomment it by removing the semi-colon(<b>;</b>) in front of it.</li>
				<li>Step 4:&nbsp;&nbsp;&nbsp;&nbsp;Restart the Apache Server.</li>
			</ul>
			For any further queries, please <a href="mailto:info@xecurify.com">contact us</a>.								
		</div>';
    }
}


function is_customer_registered_idp($registered)
{
	if(!$registered)
	{
		echo '<div style="display:block;margin-top:10px;color:red;width: 99%;
                            background-color:rgba(251, 232, 0, 0.15);
                            padding:5px;border:solid 1px rgba(255, 0, 9, 0.36);">
		        You have to <a href="'.getRegistrationURL().'">
		        Register or Login with miniOrange</a> in order to be able to Upgrade.
		      </div>';
	}
}

function get_custom_sp_attr_name_value($sp,$disabled)
{
    
	global $dbIDPQueries;
	$keyunter = 0;
	if(isset($sp) && !empty($sp))
	{
		$sp_attr = $dbIDPQueries->get_custom_sp_attr($sp->id);

		if(isset($sp_attr) && !empty($sp_attr)){
			foreach ($sp_attr as $attr) {
                echo '<tr id="crow_'.$keyunter.'">';
                echo '    <td>
                            <input  type="text" '.$disabled.' required 
                                    name="mo_idp_attribute_mapping_name['.$keyunter.']" 
                                    placeholder="Name" 
                                    value="'.$attr->mo_sp_attr_name.'"/>
                          </td>';
                echo '    <td>
                            <input  type="text" 
                                    style="width:90%;" 
                                    name="mo_idp_attribute_mapping_val['.$keyunter.']" 
                                    placeholder="Value" 
                                    value="'.htmlspecialchars($attr->mo_sp_attr_value).'"/>
                           </td>';
                $keyunter+=1;
			}
		}else{
			echo '<tr id="crow_0">
					<td>
					    <input type="text" required name="mo_idp_attribute_mapping_name[0]" placeholder="Name"/>
                    </td>
					<td>
					    <input type="text" required name="mo_idp_attribute_mapping_val[0]" style="width:90%" placeholder="Value"/>
                    </td>
				  </tr>';
		}
	}
	else{
		echo '<tr id="crow_0">
                <td><div class="mo_idp_note">Please Configure a Service Provider</div></td>
                <td><div class="mo_idp_note">Please Configure a Service Provider</div></td>
              </tr>';
	}
	$result['counter']	 = $keyunter;
	return $result;
}

function show_protocol_options($sp,$protocol_inuse)
{
    if(!MoIDPUtility::isBlank($sp)) return;
    echo'
		<h3>
			<div class="center" id="protocolDiv" style="width:100%;">
				<div class="protocol_choice_saml center '.
                     ($protocol_inuse=='SAML'? 'selected' : '') .'" data-toggle="add_sp">
				    SAML
                </div>
				<div class="protocol_choice_wsfed center '.
                     ($protocol_inuse=='WSFED'? 'selected' : '') .'" data-toggle="add_wsfed_app">
				    WS-FED
                </div>
				<div class="protocol_choice_jwt center '.
                     ($protocol_inuse=='JWT'? 'selected' : '') .'" data-toggle="add_jwt_app">
				    JWT
                </div>
			</div>
			<br/>
			<div hidden class="loader mo_idp_note">
			    <img src="'.MSI_LOADER.'">
            </div>
		</h3>';
}



function restart_tour(){
    echo "<span style='float:right;margin-top:-10px;'>
		    <button class='button button-primary button-large' onclick=\"jQuery('#show_pointers').submit();\">
		        <span   style='margin-top:5px; margin-right:5px;' 
		                class='dashicons dashicons-controls-repeat'></span>
                Restart Tour
		    </button>
	      </span>";
}