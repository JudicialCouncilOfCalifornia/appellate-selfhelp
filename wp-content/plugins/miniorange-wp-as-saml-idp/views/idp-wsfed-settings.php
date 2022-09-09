<?php

	echo'<div class="mo_idp_divided_layout mo-idp-full">
            <div class="mo_idp_table_layout mo-idp-center">
                <h2>'.$header;
                    restart_tour();
echo        '</h2><hr>';
			if(isset($sp) && !empty($sp) || $sp_exists)
			{
				show_protocol_options(isset($sp)?$sp : NULL,$protocol_inuse);

	echo		'<form name="f" method="post" action="'.$post_url.'">';

	echo			$sp_exists  ? '<input type="hidden" name="option" value="mo_add_idp" />'
                                : '<input type="hidden" name="option" value="mo_edit_idp" />';

	echo			'<input type="hidden" 
	                        name="service_provider" 
	                        value="'.(isset($sp) && !empty($sp) ? $sp->id : "").'" />
					<input  type="hidden" 
					        name="mo_idp_protocol_type" 
					        value="'.(!empty($sp) ? $sp->mo_idp_protocol_type : $protocol_inuse).'">
					
					<table id="wsFedTable" style="background-color: white" class="mo_idp_settings_table" >
						<tr>
							<td colspan="2">
                                <b>
                                    Please note down the following information from your Service Provider admin screen and 
                                    keep it handy to configure your Identity provider and map your Attributes.
                                </b>
								<ol>
									<li><b>SP Entity ID / Issuer</b></li>
									<li><b>ACS URL</b></li>
									<li><b>Username</b></li>
									<li><b>ImmutableId</b></li>
									<li><b>UPN</b></li>
									<li><b>NameID Format</b></li>
								</ol>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div style="background-color:#CBCBCB;padding:1%;border-radius:2px;">
									<i>
									    New to WS-FED? &nbsp; Looking for a documentation?
                                        Here is a guide which details how you can setup SSO between the plugin and
                                        your Microsoft Federated Domain. &nbsp;
                                        <a href="'.$wsfed_doc.'" download="">Click Here to download our guide.</a></i>
								</div>
								<br>
							</td>
						</tr>
						<tr>
							<td style="width:200px;">
							    <strong>Service Provider Name <span style="color:red;">*</span>:</strong>
                            </td>
							<td>
								<input  type="text" 
								        name="idp_sp_name" 
								        id="idpName"
								        placeholder="Service Provider Name" 
								        style="width: 95%;" 
								        value="'.(!empty($sp) ? $sp->mo_idp_sp_name : '').'" 
								        required 
								        pattern="^\w*$" 
								        title="Only alphabets, numbers and underscore is allowed"/>
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>
							    <strong>SP Entity ID or Issuer <span style="color:red;">*</span>:</strong>
                            </td>
							<td>
							    <input  type="text" 
							            name="idp_sp_issuer" 
							            placeholder="Service Provider Entity ID or Issuer" 
							            style="width: 95%;" 
							            value="'.(!empty($sp) ? $sp->mo_idp_sp_issuer : '').'" 
							            required/>
                            </td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>
							    <strong>Application Service Endpoint <span style="color:red;">*</span>:</strong>
                            </td>
							<td>
							    <input  type="text" 
							            name="idp_acs_url" 
							            placeholder="AssertionConsumerService URL" 
							            style="width: 95%;" 
							            value="'.(!empty($sp) ? $sp->mo_idp_acs_url : '').'" 
							            required/>
                            </td>
						</tr>

						<tr><td>&nbsp;</td></tr>

						<tr>
							<td><strong>NameID format <span style="color:red;">*</span>:</strong></td>
							<td>
								<select style="width:95%;" name="idp_nameid_format" required>
								    <option value="">Select a NameID Format</option>
								    <option value="1.1:nameid-format:emailAddress" 
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
                                    </option>
								</select>
								<i>
								    (<span style="color:red">NOTE: </span> Select urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress by default)
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
								        style="width:100px;margin-bottom:2%;" 
								        value="Save" 
								        class="button button-primary button-large" 
								       /> &nbsp;
								<input  type="button" 
								        name="delete" 
								        class="button button-primary button-large" 
								        '.(empty($sp) ? 'disabled' :'').' 
								        value="Delete SP Configuration" 
								        onclick = "window.location=\''.$delete_url.( !empty($sp) ? $sp->id : '' ).'\'"/>
							</td>
						</tr>
					</table>
				</form>
				<form id="add_sp" method="get" action="'.$post_url.'">
					<input type="hidden" name="page" value="'.$spSettingsTabDetails->_menuSlug.'"/>
					<input type="hidden" name="action" value="add_sp"/>
				</form>';
			}
			else
			{
	echo			'<p>Invalid SP. No Such Service Provider Found.</p>';
			}
	echo'</div>
       </div>';