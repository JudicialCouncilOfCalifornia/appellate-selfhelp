<?php
	echo '<div class="mo_idp_divided_layout mo-idp-full">
            <div class="mo_idp_table_layout mo-idp-center">
			    <h2>DELETE SP CONFIGURATION</h2><hr>';
                if(isset($sp) && !empty($sp))
                {
        echo		'<div class="">
                        Your SP configuration will be deleted forever. Are you sure you want to delete SP configuration for:
                        <p class="focus">'.$sp->mo_idp_sp_name.'</p>
                        <input  type="button" 
                                id="mo_idp_delete_sp" 
                                name="mo_idp_delete_sp" 
                                class="button button-primary button-large" 
                                value="Delete" 
                                onclick= "deleteSpSettings()"/>&nbsp;';
                }
                else
                {
	echo		    	'<p>Invalid SP. No Such Service Provider Found.</p>';
			    }
        echo			'<input type="button" 
                                class="button button-primary button-large" 
                                value="Cancel" 
                                onclick = "window.location=\''.$goback_url.'\'"/>
                    </div>
                </div>';
	if(!$disabled)
	{
		echo'<form method="post" id="mo_idp_delete_sp_settings_form" action="'.$post_url.'">
			    <input type="hidden" name="option" value="mo_idp_delete_sp_settings"/>
			    <input type="hidden" name="sp_id" value="'.$sp->id.'"/>
		     </form>';
	}