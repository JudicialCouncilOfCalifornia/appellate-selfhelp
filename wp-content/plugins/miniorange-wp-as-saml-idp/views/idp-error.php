<?php
	echo'<div class="mo_idp_table_layout">
			<h3>INVALID ACTION</h3>';
			if(isset($count) && !empty($count))
			{
	echo		'<div>
					You have reached the limit of <b>'.$count.' Service Providers(SP)</b>. 
					Contact us at info@xecurify.com or use the support form on the right if you want to increase the limit.
					<br/><br/>';
			}
			else{
	echo			'<p>Invalid Action. Please contact us at info@xecurify.com or use the support form on the right.</p>';
			}
	echo			'<input type="button" 
	                        class="button button-primary button-large" 
	                        value="Go Back" 
	                        onclick = "window.location=\''.$goback_url.'\'"/>
				 </div>
           </div>
            <form style="display:none;" id="mo_upgrade_sp_form" action="'.$post_url.'" target="_blank" method="post">
                <input type="email" name="username" value="'.$username.'" />
                <input type="text" name="redirectUrl" value="'.$payment_url.'" />
                <input type="text" name="requestOrigin" id="requestOrigin" value="'.$upgrade_plan.'"  />
            </form>';