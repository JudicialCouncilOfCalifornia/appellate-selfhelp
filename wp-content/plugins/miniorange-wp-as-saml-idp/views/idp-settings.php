<?php
if(isset($_GET['var'])) {
    $var = esc_attr(sanitize_text_field($_GET['var'])) ?? '';
} else {
    $var='';
}
		echo'<div class="mo-idp-divided-layout mo-idp-full mo-idp-bg mo-idp-margin-left mo-idp-pt">
				<div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width">';
					echo'<h2 class="mo-idp-add-new-sp">'.esc_attr($header).'</h2>';
				echo'	<hr class="mo-idp-add-new-sp-hr">';
					if (isset($sp) && !empty($sp) || $sp_exists) {
						show_protocol_options($sp_exists ? NULL : $sp, $protocol_inuse);
			echo	'<input type="hidden" name="service_provider" value="'.(isset($sp) && !empty($sp) ? $sp->id : "").'" />
					<input type="hidden" name="mo_idp_protocol_type" value="'.$protocol_inuse.'">
					';
					echo '<form name="saml_form" class="mo-idp-bg-pad" method="post" action="' . esc_url(admin_url()) . 'admin.php?page=idp_configure_idp' . '" enctype="multipart/form-data">';	
						echo'<ul class="mo-idp-switch-tab mo-idp-text-center" >
							<li  onclick="moidpmanualhandler()" id="mo-idp-manual-upload" class="mo-idp-current-tab">
								<a class="mo-idp-upload-data-anchor" style="position:relative;top: -0.5rem !important;"><span class="mo-idp-enter-data mo-idp-text-color">Enter SP Data Manually</span></a>
							</li>
							<li style="font-size:1.25rem;"><b>OR</b></li>';
							echo'<li class="" onclick="moidpautohandler()" id="mo-idp-auto-upload">
								<a style="position:relative;top: -0.5rem !important; "  &action=upload_idp_metadata&var='.$protocol_inuse.'" class="mo-idp-upload-data-anchor" ><span class="mo-idp-enter-data">Upload SP Metadata</span></a>
							</li>';
						echo'</ul>	
							<div id="mo-idp--auto-upload" >
								<div class="mo-idp-center mo-idp-settings-table">
									<div>
										<table class="mo-idp-settings-table mo-idp-sp-data-table">
											<tr>';
											echo'<form name="saml_form" method="post" action="'.esc_url(admin_url()).'admin.php?page=idp_configure_idp'. '" enctype="multipart/form-data">
											<input type="hidden" name="mo_idp_protocol_type" value="'.esc_attr($var).'">';
											echo'<tr class="mo-idp-home-card-link">
												<td width="30%"><strong>Service Provider Name<span class="mo-idp-red">*</span> :</strong></td>
												<td><input type="text" name="idp_sp_name" class="mo-idp-table-textbox mo-idp-table-input mo-idp-settings-table " placeholder="Service Provider Name " pattern="\w+" title="Only alphabets, numbers and underscore is allowed"  value="" required style="width: 33rem !important;" /></td>
											</tr>
											<tr><td>&nbsp;</td></tr>
											<tr>
												<input type="hidden" name="option" value="saml_idp_upload_metadata" />
												<input type="hidden" name="action" value="upload_idp_metadata" />
													<td class="mo-idp-home-card-link"><b>Upload Metadata:</b></td>
													<td colspan="2">
												<div style="display:inline-flex;justify-content:center;align-items:center;">
												<input type="file" name="metadata_file" class="mo-idp-home-card-link" style="width:33rem;"/>
													<div style="position:relative;margin-left:3.1rem;">
													<input type="submit" value="Upload" class="button button-primary" style="border: none;font-size: 1.1rem;padding: 0.1rem 1.5rem 0.1rem 0rem;color: #fff!important;margin:0rem;border-radius:3px;background: #2271B1;min-width:9rem"><svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="position: absolute;left:6.3rem;top: 0.8rem;
											color: white;"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"></path>
																					<path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"></path>
																				</svg>
												</input>
												</div>
												</div>';
												echo'	</td>
											</tr>	
											<tr>
													<td colspan="2">
														<div class="mo-idp-text-center mo-idp-or-block"> 
															<div class="mo-idp-mt-5" style="position:relative">
																<hr class="mo-idp-add-new-sp-hr" style="position:relative;border-top:3px solid #6c757d!important;width:15rem;">
																<span class="mo-idp-metadata-or mo-idp-or-resp mo-idp-bg-secondary mo-idp-rounded-circle mo-idp-text-white">OR</span>
															</div>
														</div>
													</td>
											</tr>
											<tr><td>&nbsp;</td></tr>
											<tr>
												<input type="hidden" name="option" value="saml_idp_upload_metadata" />
												<input type="hidden" name="action" value="fetch_metadata" />
												<td width="20%" class="mo-idp-home-card-link"><b>Enter metadata URL:</b></td>
												<td>
												<div style="display:inline-flex;justify-content:center;align-items:center">
													<div>
														<input type="url" name="metadata_url" class="mo-idp-table-textbox mo-idp-table-input mo-idp-settings-table" placeholder="Enter metadata URL of your SP" style="width:33rem !important" />
													</div>
														<input type="submit" class="button button-primary" style="font-size: 1rem;padding:2px 17px;margin-left:3.1rem; " value="Fetch Metadata"/>	
												</div>
												</td>
											</tr>
											<tr><td>&nbsp;</td></tr>
											<tr><td>&nbsp;</td></tr>
										</form>
										</table>
									</div>
								</div>
							</div>
							';
							echo	'<form name="f" id="" method="post" action="' .esc_url($post_url) . '" class="mo-idp-mt-5">';

							echo	$sp_exists  ? '
					<input type="hidden" name="option" value="mo_add_idp" />'
			: '<input type="hidden" name="option" value="mo_edit_idp" />';

		echo	'<input type="hidden" name="service_provider" value="' . (isset($sp) && !empty($sp) ? esc_attr($sp->id) : "") . '" />
					<input type="hidden" name="mo_idp_protocol_type" value="' . esc_attr($protocol_inuse) . '">';


						echo '

							<div id="mo-idp--manual_upload" >			
								<table class="mo-idp-settings-table mo-idp-sp-data-table">
									<tr>
										<td>
											<strong class="mo-idp-home-card-link">Service Provider Name<span class="mo-idp-red">*</span> :</strong>
										</td>
										<td>';
											echo' <input  type="text"
													name="idp_sp_name"
													id="idpName" class="mo-idp-table-input"
													placeholder="Service Provider Name"
													value="'.(!empty($sp) ? esc_attr($sp->mo_idp_sp_name) : '').'"
													required
													pattern="^\w*$"
													title="Only alphabets, numbers and underscore is allowed"/>';
										echo' </td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr>
										<td>
											<strong class="mo-idp-home-card-link">SP Entity ID or Issuer<span class="mo-idp-red">*</span> :</strong>
										</td>
										<td>';
											echo'<input  type="text"
													name="idp_sp_issuer" class="mo-idp-table-input"
													placeholder="Service Provider Entity ID or Issuer"
													value="'.(!empty($sp) ? esc_attr($sp->mo_idp_sp_issuer) : '').'"
													required />';
										echo'</td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr>
										<td>
											<strong class="mo-idp-home-card-link">ACS URL<span class="mo-idp-red">*</span> :</strong>
										</td>
										<td>';
										echo'	<input  type="text"
													name="idp_acs_url"
													placeholder="AssertionConsumerService URL"
													class="mo-idp-table-input" value="'.(!empty($sp) ? esc_url($sp->mo_idp_acs_url) : '').'"
													required />
										</td>';
									echo' </tr>
									<tr><td>&nbsp;</td></tr>
									<tr>
										<td><strong class="mo-idp-home-card-link">NameID format<span class="mo-idp-red">*</span> :</strong></td>
										<td>
											<select name="idp_nameid_format" required class="mo-idp-select mo-idp-table-input" >
												<option value="">Select a NameID Format</option>';
												echo'<option value="1.1:nameid-format:emailAddress"
														'.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'emailAddress') ? 'selected' : '').'>
														urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress
												</option>';
												echo'<option value="1.1:nameid-format:unspecified"
														'.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'unspecified') ? 'selected' : '').'>
														urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified
												</option>';
												echo'<option value="2.0:nameid-format:transient"
														'.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'transient') ? 'selected' : '').'>
														urn:oasis:names:tc:SAML:2.0:nameid-format:transient
												</option>';
												echo'<option value="2.0:nameid-format:persistent"
														'.(!empty($sp) && strpos($sp->mo_idp_nameid_format,'persistent') ? 'selected' : '').'>
														urn:oasis:names:tc:SAML:2.0:nameid-format:persistent
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
										<td>
											<strong class="mo-idp-home-card-link">Default Relay State (optional)</span>:</strong>
										</td>
										<td>';
											echo '<input  type="text"
													name="idp_default_relayState"
													placeholder="Default Relay State"
													class="mo-idp-table-input"
													value="'.(!empty($sp) ? esc_attr($sp->mo_idp_default_relayState) : '').'"
													/>';
										echo'</td>
									</tr>
									<tr><td>&nbsp;</td></tr>
									<tr>
									<td><strong class="mo-idp-home-card-link">Assertion Signed:</strong></td>
									<td style="display:inline-flex;align-items:center">';
										echo'<input  type="checkbox"
												name="idp_assertion_signed"
												value="1" class="mo-idp-assertion-check"
												'.(!empty($sp) && $sp->mo_idp_assertion_signed ? 'checked' : '').'
												/>';
										echo'<span class="mo-idp-assertion-check-desc">Check if you want to sign the SAML Assertion.</span>
									</td>
									</tr>
									<tr><td>&nbsp;</td></tr>
								</table>

								<table class="mo-idp-settings-table">						   
									<tr class="hide_button_top">
										<td>&nbsp;</td>
										<td>
											<br/>
											<input  type="submit"
													name="submit"
													id="Save"
													style="margin-bottom:2%;"
													value="Save"
													class="button button-primary mo-idp-button-large"
													/> &nbsp;';
										echo'	<input  type="button"
													name="test"
													id="testConfig"
													title="You can only test your Configuration after saving your Service Provider Settings."
													onclick="showTestWindow(\''.esc_url($test_window).'\');"
													'.(empty($sp) ? 'disabled' : '').'
													value="Test configuration"
													class="button button-primary mo-idp-button-large"
													style="margin-right: 1%;"/>';
										echo'	<input  type="button"
													name="delete"
													class="button button-primary mo-idp-button-large"
													'.(empty($sp) ? "disabled" : "").'
													value="Delete SP Configuration"
													onclick = "window.location=\''.esc_url($delete_url).( !empty($sp) ? esc_attr($sp->id) : '' ).'\'"/>';
										echo'</td>
									</tr>
								</table>
							</div>';				
				echo'<a href="'.esc_url($license_url).'" class="mo-idp-upload-data-anchor">';
				echo'	<div id="mo-idp-advt-premium-feature">';
						echo'<img class="mo-idp-advt-premium-feature-lock" src="'.MSI_LOCK.'"/>';
						echo'<h2 class="mo-idp-advt-premium-feature-head">Premium Features</h2>
						<div class="mo-idp-advt-premium-feature-row1 mo-idp-flex mo-idp-mt-5">
							<div> X.509 Certificate (For Encrypted Assertion)</div>
							<div> X.509 Certificate (For Signed Request)</div>
						</div>
						<div class="mo-idp-advt-premium-feature-row2 mo-idp-flex mo-idp-mt-5">
							<div>Single Logout URL</div>
							<div>Encrypted Assertion</div>
							<div>Response Signed</div>
						</div>
					</div>	
				</a>

				</form>';
				echo'<form id="add_sp" method="get" action="'.esc_url($post_url).'">
					<input type="hidden" name="page" value="'.esc_attr($spSettingsTabDetails->_menuSlug).'"/>
					<input type="hidden" name="action" value="add_wsfed_sp"/>
				</form>';

			}else
		{
			echo	'<p>Invalid SP. No Such Service Provider Found.</p>';
		}
		echo'</div>
			</div>

			
		';

