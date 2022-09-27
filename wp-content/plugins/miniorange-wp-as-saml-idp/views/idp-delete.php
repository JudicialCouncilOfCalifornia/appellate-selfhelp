<?php
	echo '<div class="mo-idp-divided-layout mo-idp-full mo-idp-bg mo-idp-margin-left mo-idp-pt">
            <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width">
			    <h2 class="mo-idp-add-new-sp">Delete SP Configuration</h2>
                            <hr class="mo-idp-add-new-sp-hr">
                            <br>';
                if(isset($sp) && !empty($sp))
                {
        echo		'<div class="">
                                <span class="mo-idp-mt-5 mo-idp-home-card-link" >
                                        Your SP configuration will be deleted forever. Are you sure you want to delete SP configuration for:
                                </span>';
                    echo'    <p class="mo-idp-focus">'.esc_attr($sp->mo_idp_sp_name).'</p>';
                      echo'  <input  type="button" 
                                id="mo_idp_delete_sp" 
                                name="mo_idp_delete_sp" 
                                class="button button-primary button-large mo-idp-button-large" 
                                value="Delete" 
                                style="width: 11% !important;"
                                onclick= "deleteSpSettings()"/>&nbsp;';
                }
                else
                {
	echo		    	'<p>Invalid SP. No Such Service Provider Found.</p>';
			    }
        echo			'<input type="button" 
                                class="button button-primary button-large mo-idp-button-large" 
                                value="Cancel" 
                                style="width: 11% !important;"
                                onclick = "window.location=\''.esc_url($goback_url).'\'"/>';
                 echo'   </div>
                </div>';
	if(!$disabled)
	{
		echo'<form method="post" id="mo_idp_delete_sp_settings_form" action="'.esc_url($post_url).'">
			    <input type="hidden" name="option" value="mo_idp_delete_sp_settings"/>
			    <input type="hidden" name="sp_id" value="'.esc_attr($sp->id).'"/>
		     </form>';
	}