<?php
	echo'<div class="mo-idp-table-layout">
			<h3>INVALID ACTION</h3>';
			if(isset($count) && !empty($count))
			{
	echo		'<div>
					You have reached the limit of <b>'.esc_attr($count).' Service Providers(SP)</b>. 
					Contact us at samlsupport@xecurify.com or use the support form if you want to increase the limit.
					<br/><br/>';
			}
			else{
	echo			'<p>Invalid Action. Please contact us at samlsupport@xecurify.com or use the support form.</p>';
			}
	echo			'<input type="button" 
	                        class="button button-primary button-large" 
	                        value="Go Back" 
	                        onclick = "window.location=\''.esc_url($goback_url).'\'"/>';
				echo' </div>
           </div>';
           echo' <form style="display:none;" id="mo_upgrade_sp_form" action="'.esc_url($post_url).'" target="_blank" method="post">
                <input type="email" name="username" value="'.esc_attr($username).'" />
                <input type="text" name="redirectUrl" value="'.esc_url($payment_url).'" />
                <input type="text" name="requestOrigin" id="requestOrigin" value="'.esc_attr($upgrade_plan).'"  />
            </form>';