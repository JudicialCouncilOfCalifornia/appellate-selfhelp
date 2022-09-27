<?php

	echo'<div class="mo-idp-divided-layout mo-idp-full mo-idp-bg mo-idp-margin-left mo-idp-pt">
            <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width">';
            echo '<h2 class="mo-idp-add-new-sp">'.esc_attr($header);
				if(!empty($sp_list))
				{
	echo			'<span style="float:right;margin-top:-0.625rem;">
						<a href="'.esc_url($goback_url).'">
							<input type="button" value="Go Back" class="button button-primary mo-idp-button-large">
						</a>
					</span>';
				}

	echo	 '  </h2>
                <hr class="mo-idp-add-new-sp-hr"/>';
			if(isset($sp) && !empty($sp) || $sp_exists)
			{
                show_protocol_options($sp,$protocol_inuse);
	echo		'
                <div class="mo-idp-sp-data-manual mo-idp-bg" >';
                   echo' <a href="'.esc_url($license_url).'" class="mo-idp-upload-data-anchor">';
                        echo' <div id="mo-idp-advt-premium-feature">';
                            echo'  <img class="mo-idp-advt-premium-feature-lock" src="'.MSI_URL.'includes/images/lock.png"/>';
                                echo'     <form name="f" method="post" action="'.esc_url($post_url).'">';
                echo			$sp_exists ? '<input type="hidden" name="option" value="mo_add_idp" />' : '<input type="hidden" name="option" value="mo_edit_idp" />';
                echo			'<input type="hidden" name="service_provider" value="'.(isset($sp) && !empty($sp) ? esc_attr($sp->id) : "").'" />
                                <input  type="hidden" 
                                        name="mo_idp_protocol_type" 
                                        value="'.(!empty($sp) ? esc_attr($sp->mo_idp_protocol_type) : esc_attr($protocol_inuse)).'">       
                            </form>
                            <form id="add_sp" method="get" action="'.esc_url($post_url).'">
                                <input type="hidden" name="page" value="'.esc_attr($spSettingsTabDetails->_menuSlug).'"/>
                                <input type="hidden" name="action" value="add_sp"/>
                            </form>';
                          echo'  <div>
                                <h2 class="mo-idp-advt-premium-feature-head-jwt-prem">Premium Features</h2>
                                <div class="mo-idp-advt-premium-feature-row1 mo-idp-flex mo-idp-mt-5">
                                    <div>App JWT Endpoint URL</div>
                                    <div>Return to URL (optional)</div>
                                </div>
                                <div class="mo-idp-advt-premium-feature-row2 mo-idp-flex mo-idp-mt-5">
                                    <div>Hashing Algorithm</div>
                                    <div>Shared Secret</div>
                                </div>
				            </div>
                    </div>
                    </a>
                </div>';
                
			}
			else
			{
	echo		    '<p>Invalid SP. No Such Service Provider Found.</p>';
			}
	echo'</div>
      </div>';