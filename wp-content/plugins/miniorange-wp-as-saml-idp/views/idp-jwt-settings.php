<?php

	echo'<div class="mo_idp_divided_layout mo-idp-full">
            <div class="mo_idp_table_layout mo-idp-center">
                <h2>'.$header;
				if(!empty($sp_list))
				{
	echo			'<span style="float:right;margin-top:-10px;">
						<a href="'.$goback_url.'">
							<input type="button" value="Go Back" class="button button-primary button-large">
						</a>
					</span>';
				}

	echo	 '  </h2>
                <hr/>';
			if(isset($sp) && !empty($sp) || $sp_exists)
			{
                show_protocol_options($sp,$protocol_inuse);

	echo		'<div class="mo_idp_premium_option_text">
                    <span style="color:red;">*</span> This is a premium feature. Check 
                    <a href="'.$license_url.'">Licensing Tab</a> to learn more.
                </div>
                <div class="mo_premium_option">
                    <form name="f" method="post" action="'.$post_url.'">';
        echo			$sp_exists ? '<input type="hidden" name="option" value="mo_add_idp" />' : '<input type="hidden" name="option" value="mo_edit_idp" />';
        echo			'<input type="hidden" name="service_provider" value="'.(isset($sp) && !empty($sp) ? $sp->id : "").'" />
                        <input  type="hidden" 
                                name="mo_idp_protocol_type" 
                                value="'.(!empty($sp) ? $sp->mo_idp_protocol_type : $protocol_inuse).'">
                        <table class="mo_idp_settings_table">
                            <tr>
                                <td colspan="2">
                                    <b>
                                        Please note down the following information from your Service Provider admin screen
                                        and keep it handy to configure your Identity provider and map your Attributes.
                                    </b>
                                    <ol>
                                        <li><b>JWT Endpoint URL</b></li>
                                        <li><b>Shared Secret</b></li>
                                        <li><b>Return To URL</b></li>
                                    </ol>
                                </td>
                            </tr>
                            <tr>
                                <td style="width:200px;">
                                    <strong>Application Name <span style="color:red;">*</span>:</strong>
                                </td>
                                <td></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td>
                                    <strong>
                                        App JWT Endpoint URL <span style="color:red;">*</span>:
                                    </strong>
                                </td>
                                <td></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td>
                                    <strong>
                                        Shared Secret <span style="color:red;">*</span>:
                                    </strong>
                                </td>
                                <td></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td>
                                    <strong>
                                        Hashing Algorithm <span style="color:red;">*</span>:
                                    </strong>
                                </td>
                                <td></td>
                            </tr>
                            <tr><td>&nbsp;</td></tr>
                            <tr>
                                <td>
                                    <strong>Return to URL: (optional)</span>:</strong>
                                </td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td></td>
                            </tr>
                        </table>
                    </form>
                    <form id="add_sp" method="get" action="'.$post_url.'">
                        <input type="hidden" name="page" value="'.$spSettingsTabDetails->_menuSlug.'"/>
                        <input type="hidden" name="action" value="add_sp"/>
                    </form>
                    <input  type="checkbox"  
                            '.(!$registered || empty($sp) ? 'disabled title="Disabled. Configure your Identity Provider"' : '').' 
                            onchange="window.location=\''.$sp_page_url.'\'" />
                        Check this option if you have Configured your Identity Provider settings.';
    echo'       </div>';
			}
			else
			{
	echo		    '<p>Invalid SP. No Such Service Provider Found.</p>';
			}
	echo'</div>
      </div>';