<?php

	echo'<div class="mo_idp_divided_layout mo-idp-full">
            <div class="mo_idp_table_layout mo-idp-center">
                <h2>'.$header;
                    restart_tour();
    echo        '</h2><hr>';
			if(isset($sp) && !empty($sp) || $sp_exists)
			{
				show_protocol_options( $sp_exists ? NULL : $sp,$protocol_inuse);

	echo		'<form name="f" method="post" action="'.$post_url.'">';


	echo			$sp_exists  ? '<input type="hidden" name="option" value="mo_add_idp" />'
                                : '<input type="hidden" name="option" value="mo_edit_idp" />';

	echo		    '<input type="hidden" name="service_provider" value="'.(isset($sp) && !empty($sp) ? $sp->id : "").'" />
					<input type="hidden" name="mo_idp_protocol_type" value="'.$protocol_inuse.'">
					<table class="mo_idp_settings_table">
						<tr>
							<td colspan="2">
								<b>
								    Please note down the following information from your Service Provider admin screen
								    and keep it handy to configure your Identity provider.
								</b>
								<ol>
									<li><b>SP Entity ID / Issuer</b></li>
									<li><b>ACS URL</b></li>
									<li><b>X.509 Certificate for Signing if you are using HTTP-POST Binding. (Optional)</b></li>
									<li><b>X.509 Certificate for Encryption. (Optional)</b></li>
									<li><b>NameID Format</b></li>
								</ol>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<div style="background-color:#CBCBCB;padding:1%;border-radius:2px;">
									<i>
									    New to SAML? &nbsp; Looking for a documentation? &nbsp;
									    <a href="'.$saml_doc.'" download="">Click Here to download our guide.</a>
                                    </i>
								</div>
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
							            required />
                            </td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>
							    <strong>ACS URL <span style="color:red;">*</span>:</strong>
                            </td>
							<td>
							    <input  type="text"
							            name="idp_acs_url"
							            placeholder="AssertionConsumerService URL"
							            style="width: 95%;"
							            value="'.(!empty($sp) ? $sp->mo_idp_acs_url : '').'"
							            required />
                            </td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><strong>Single Logout URL (optional):</strong></td>
							<td>
								<span style="color:red;">*</span> This is a premium feature. Check
								<a href="'.$license_url.'">Licensing Tab</a> to learn more.
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>
							    <strong>
							        X.509 Certificate (optional):<br/>
							        <i><span style="font-size:11px;">(For Signed Request)</span></i>
                                </strong>
                            </td>
							<td>
							    <span style="color:red;">*</span> This is a premium feature. Check
							    <a href="'.$license_url.'">Licensing Tab</a> to learn more.
                            </td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>&nbsp;</td>
							<td>
							    <i>
							        <b>NOTE:</b> Format of the certificate:<br/>
							        <b>-----BEGIN CERTIFICATE-----
							        <br/>XXXXXXXXXXXXXXXXXXXXXXXXXXX<br/>
							        -----END CERTIFICATE-----</b>
							    </i>
							    <br/>
							 </td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>
							    <strong>
							        X.509 Certificate (optional):<br/>
							        <i><span style="font-size:11px;">(For Encrypted Assertion)</span></i>
                                </strong>
                            </td>
							<td>
							    <span style="color:red;">*</span> This is a premium feature. Check
							    <a href="'.$license_url.'">Licensing Tab</a> to learn more.
                            </td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>&nbsp;</td>
							<td>
							    <i>
							        <b>NOTE:</b> Format of the certificate:<br/>
							        <b>-----BEGIN CERTIFICATE-----
							        <br/>XXXXXXXXXXXXXXXXXXXXXXXXXXX<br/>
							        -----END CERTIFICATE-----</b>
							    </i>
							    <br/>
							 </td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><strong>NameID format <span style="color:red;">*</span>:</strong></td>
							<td>
							    <!-- todo: Optimize this and get values from controller -->
								<select style="width:95%;" name="idp_nameid_format" required >
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
								            urn:oasis:names:tc:SAML:2.0:nameid-format:transient
                                    </option>
								    <option value="2.0:nameid-format:persistent"
								            '.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'persistent') ? 'selected' : '').'>
								            urn:oasis:names:tc:SAML:2.0:nameid-format:persistent
                                    </option>
								</select>
								<i>
								    (<span style="color:red">NOTE: </span> Select urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress by default)
                                </i>
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>
							    <strong>Default Relay State: (optional)</span>:</strong>
                            </td>
							<td>
							    <input  type="text"
							            name="idp_default_relayState"
							            placeholder="Default Relay State"
							            style="width: 95%;"
							            value="'.(!empty($sp) ? $sp->mo_idp_default_relayState : '').'"
							            />
                            </td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><strong>Response Signed:</strong></td>
							<td>
							    <span style="color:red;">*</span> This is a premium feature. Check
							    <a href="'.$license_url.'">Licensing Tab</a> to learn more.
                            </td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><strong>Assertion Signed:</strong></td>
							<td>
							    <input  type="checkbox"
                                        name="idp_assertion_signed"
                                        value="1"
                                        '.(!empty($sp) && $sp->mo_idp_assertion_signed ? 'checked' : '').'
                                        />
							    Check if you want to sign the SAML Assertion.
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td><strong>Encrypted Assertion:</strong></td>
							<td>
							    <span style="color:red;">*</span> This is a premium feature. Check
							    <a href="'.$license_url.'">Licensing Tab</a> to learn more.
                            </td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
							    <br/>
								<input  type="submit"
                                        name="submit"
                                        id="Save"
                                        style="width:100px;margin-bottom:2%;"
                                        value="Save"
                                        class="button button-primary button-large"
                                        /> &nbsp;
								<input  type="button"
								        name="test"
								        id="testConfig"
								        title="You can only test your Configuration after saving your Service Provider Settings."
								        onclick="showTestWindow(\''.$test_window.'\');"
								        '.(empty($sp) ? 'disabled' : '').'
								        value="Test configuration"
								        class="button button-primary button-large"
								        style="margin-right: 2%;"/>
								<input  type="button"
								        name="delete"
								        class="button button-primary button-large"
								        '.(empty($sp) ? "disabled" : "").'
								        value="Delete SP Configuration"
								        onclick = "window.location=\''.$delete_url.( !empty($sp) ? $sp->id : '' ).'\'"/>
							</td>
						</tr>
					</table>
				</form>
				<form id="add_sp" method="get" action="'.$post_url.'">
					<input type="hidden" name="page" value="'.$spSettingsTabDetails->_menuSlug.'"/>
					<input type="hidden" name="action" value="add_wsfed_sp"/>
				</form>';
			}
			else
			{
	echo			'<p>Invalid SP. No Such Service Provider Found.</p>';
			}
	echo'</div>
        </div>';