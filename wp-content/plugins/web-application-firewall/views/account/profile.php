<?php
	
echo'
    <div class="mo_wpns_divided_layout">
        <div class="mo_wpns_setting_layout" >
            <div>
                <h4>Thank You for registering with miniOrange.</h4>
                <h3>Your Profile</h3>
                <table border="1" style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0px 0px 0px 10px; margin:2px; width:85%">
                    <tr>
                        <td style="width:45%; padding: 10px;">Username/Email</td>
                        <td style="width:55%; padding: 10px;">'.esc_attr($email).'</td>
                    </tr>
                    <tr>
                        <td style="width:45%; padding: 10px;">Customer ID</td>
                        <td style="width:55%; padding: 10px;">'.esc_attr($key).'</td>
                    </tr>
                    <tr>
                        <td style="width:45%; padding: 10px;">API Key</td>
                        <td style="width:55%; padding: 10px;">'.esc_attr($api).'</td>
                    </tr>
                    <tr>
                        <td style="width:45%; padding: 10px;">Token Key</td>
                        <td style="width:55%; padding: 10px;">'.esc_attr($token).'</td>
                    </tr>
                </table>
                <br>
                <form name="f" method="post" action="">
                    <input type="hidden" name="nonce" value="'.wp_create_nonce('mo-wpns-log-out').'"/>
                    <input type="hidden" name="option" value="mo_wpns_log_out"/>
                    <input type="submit" name="submit" value="Remove Account" class="button button-primary"/>
                </form>
                <br/>

                <p><a target="_blank" href="https://login.xecurify.com/moas/idp/resetpassword" >Click here</a> if you forgot your password to your miniOrange account.</p>
            </div>
        </div>
    </div>

	
	<script>
		jQuery(document).ready(function(){
			$(\'a[href="#mo_wpns_forgot_password_link"]\').click(function(){
				$("#forgot_password_form").submit();
			});
		});
	</script>';