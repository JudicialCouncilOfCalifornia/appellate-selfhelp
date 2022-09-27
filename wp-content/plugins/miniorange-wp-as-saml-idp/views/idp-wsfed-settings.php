<?php

	echo'<div class="mo-idp-divided-layout mo-idp-full mo-idp-bg mo-idp-margin-left mo-idp-pt">
            <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width">';
                echo'<h2 class="mo-idp-add-new-sp">'.esc_attr($header).'</h2>';
			echo'<hr class="mo-idp-add-new-sp-hr">';
			if(isset($sp) && !empty($sp) || $sp_exists)
			{
				show_protocol_options(isset($sp)?$sp : NULL,$protocol_inuse);

	echo		'<form name="f" method="post" class="mo-idp-bg-pad" action="'.esc_url($post_url).'" >';

	echo			$sp_exists  ? '<input type="hidden" name="option" value="mo_add_idp" />'
                                : '<input type="hidden" name="option" value="mo_edit_idp" />';

	echo			'<input type="hidden" 
	                        name="service_provider" 
	                        value="'.(isset($sp) && !empty($sp) ? esc_attr($sp->id) : "").'" />
					<input  type="hidden" 
					        name="mo_idp_protocol_type" 
					        value="'.(!empty($sp) ? esc_attr($sp->mo_idp_protocol_type) : esc_attr($protocol_inuse)).'">';
					
				echo' <table id="wsFedTable" class="mo-idp-settings-table mo-idp-mt-5" >
						<tr>
							<td>
							    <strong class="mo-idp-home-card-link">Service Provider Name<span class="mo-idp-red">*</span> :</strong>
                            </td>
							<td>';
							echo'	<input  type="text" 
								        name="idp_sp_name" 
								        id="idpName"
								        placeholder="Service Provider Name" 
								        class="mo-idp-table-input" 
								        value="'.(!empty($sp) ? esc_attr($sp->mo_idp_sp_name) : '').'" 
								        required 
								        pattern="^\w*$" 
								        title="Only alphabets, numbers and underscore is allowed"/>';
						echo'	</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>
							    <strong class="mo-idp-home-card-link">SP Entity ID or Issuer<span class="mo-idp-red">*</span> :</strong>
                            </td>
							<td>';
							echo'    <input  type="text" 
							            name="idp_sp_issuer" 
										class="mo-idp-table-input"
							            placeholder="Service Provider Entity ID or Issuer" 
							            value="'.(!empty($sp) ? esc_attr($sp->mo_idp_sp_issuer) : '').'" 
							            required/>';
                            echo'</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>
							    <strong class="mo-idp-home-card-link">Application Service Endpoint<span class="mo-idp-red">*</span> :</strong>
                            </td>
							<td>';
							  echo'  <input  type="text" 
							            name="idp_acs_url" 
							            placeholder="AssertionConsumerService URL" 
										class="mo-idp-table-input"
										value="'.(!empty($sp) ? esc_url($sp->mo_idp_acs_url) : '').'" 
							            required/>';
                            echo'</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><strong class="mo-idp-home-card-link" >NameID format<span class="mo-idp-red">*</span> :</strong></td>
							<td>
								<select class="mo-idp-table-input" name="idp_nameid_format" style="max-width:none;" required >
								    <option value="">Select a NameID Format</option>';
								   echo' <option value="1.1:nameid-format:emailAddress" 
                                            '.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'emailAddress') ? 'selected' : '').'>
                                            urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress
                                    </option>
								    <option value="1.1:nameid-format:unspecified" 
								            '.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'unspecified') ? 'selected' : '').'>
								            urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified
                                    </option>
								    <option value="2.0:nameid-format:transient" 
                                            '.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'transient') ? 'selected' : '').'>
                                            urn:oasis:names:tc:SAML:1.1:nameid-format:transient
                                    </option>
								    <option value="2.0:nameid-format:persistent" 
								            '.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'persistent') ? 'selected' : '').'>
								            urn:oasis:names:tc:SAML:1.1:nameid-format:persistent
                                    </option>';
								echo'</select>
								<br>
								<i>
								    (<span class="mo-idp-red">NOTE: </span> Select urn:oasis:names:tc:SAML:1.1:nameid-format:<b>emailAddress</b> by default)
                                </i>		
							</td>
						</tr>	
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>&nbsp;</td>
							<td><br/>
								<input  type="submit" 
								        name="submit"
								        id="Save" 
								        style="margin-bottom:2%;" 
								        value="Save" 
								        class="button button-primary mo-idp-button-large" 
								       /> &nbsp;';
								echo '<input  type="button" 
								        name="delete" 
								        class="button button-primary mo-idp-button-large" 
								        '.(empty($sp) ? 'disabled' :'').' 
								        value="Delete SP Configuration" 
								        onclick = "window.location=\''.esc_url($delete_url).( !empty($sp) ? esc_attr($sp->id) : '' ).'\'"/>';
						echo'	</td>
						</tr>
					</table>
				</form>';
				echo'
				<form id="add_sp" method="get" action="'.esc_url($post_url).'">
					<input type="hidden" name="page" value="'.esc_attr($spSettingsTabDetails->_menuSlug).'"/>
					<input type="hidden" name="action" value="add_sp"/>
				</form>';
			}
			else
			{
	echo			'<p>Invalid SP. No Such Service Provider Found.</p>';
			}
	echo'</div>
       </div>';