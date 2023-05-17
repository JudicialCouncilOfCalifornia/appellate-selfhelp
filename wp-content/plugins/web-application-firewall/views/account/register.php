<?php	


echo'<!--Register with miniOrange-->
	<form name="f" method="post" action="" class="mo_register" hidden>
		<input type="hidden" name="option" value="mo_wpns_register_customer" />
		<div class="mo_wpns_divided_layout">
			<div class="mo_wpns_setting_layout">
				<h3>Register with miniOrange</h3>
				<p>Just complete the short registration below to configure Wp Security Pro plugin. Please enter a valid email id that you have access to.</p>
				<table class="mo_wpns_settings_table">
					<tr>
						<td><b><font color="#FF0000">*</font>Email:</b></td>
						<td><input class="mo_wpns_table_textbox" type="email" name="email"
							required placeholder="person@example.com"
							value="'.esc_attr($current_user->user_email).'" /></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>Password:</b></td>
						<td><input class="mo_wpns_table_textbox" required type="password"
							name="password" placeholder="Choose your password (Min. length 6)" /></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
						<td><input class="mo_wpns_table_textbox" required type="password"
							name="confirmPassword" placeholder="Confirm your password" /></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><br />
						<input type="submit" name="submit" value="Register" style="width:100px;" class="button button-primary button-large" />
						<a style="text-decoration:underline; margin-left:5px; width:100px;" class="mo_toggle" >Already have account? Login here</a>
						</td>
							
					</tr>
				</table>
			</div>
		</div>
	</form>';


	echo'	<form name="f" method="post" action="" class="mo_login">
			<input type="hidden" name="option" value="mo_wpns_verify_customer" />
			<div class="mo_wpns_divided_layout">
				<div class="mo_wpns_setting_layout">
					<h3>Login with miniOrange</h3>
					<p><b>It seems you already have an account with miniOrange. Please enter your miniOrange email and password.</td><a target="_blank" href="https://login.xecurify.com/moas/idp/resetpassword"> Click here if you forgot your password?</a></b></p>
					<table class="mo_wpns_settings_table">
						<tr>
							<td><b><font color="#FF0000">*</font>Email:</b></td>
							<td><input class="mo_wpns_table_textbox" type="email" name="email"
								required placeholder="person@example.com"
								value="'.esc_attr($admin_email).'" /></td>
						</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Password:</b></td>
							<td><input class="mo_wpns_table_textbox" required type="password"
								name="password" placeholder="Enter your miniOrange password" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
							<input type="submit" value="Login" class="button button-primary button-large"/>
							<a style="text-decoration:underline; margin-left:5px; width:100px;" class="mo_toggle" >New user ? Register here </a>
							</td>
						</tr>
					</table>
				</div>
			</div>
		</form>
		<form id="forgot_password_form" method="post" action="">
			<input type="hidden" name="option" value="mo_wpns_reset_password" />
		</form>
		<form id="cancel_form" method="post" action="">
			<input type="hidden" name="option" value="mo_wpns_cancel" />
		</form>';
?>

<script>
			jQuery(document).ready(function(){
				 jQuery(".mo_toggle").click(()=>{
					jQuery(".mo_register").toggle();
					jQuery(".mo_login").toggle();
				 });

				jQuery('#mo_wpns_forgot_password_link').click(function(){
					alert("Hello");
					jQuery("#forgot_password_form").submit();
				});
			});
</script>