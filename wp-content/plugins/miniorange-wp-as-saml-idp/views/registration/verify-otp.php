<?php
	echo'<form name="f" method="post" id="otp_form" action="">
			<input type="hidden" name="option" value="mo_idp_validate_otp" />
				<div class="mo_idp_table_layout">
					<table class="mo_idp_settings_table">
						<h3>Verify Your Email</h3>
						<tr>
							<td><b><font color="#FF0000">*</font>Enter OTP:</b></td>
							<td colspan="3">
							    <input  class="mo_idp_table_textbox" 
							            autofocus="true" 
							            type="text" 
							            name="otp_token" required 
							            placeholder="Enter OTP" 
							            style="width:40%;" 
							            pattern="[0-9]{6,8}" 
							            title="Only 6 digit numbers are allowed"/>
							 &nbsp;&nbsp;<a style="cursor:pointer;" onclick="document.getElementById(\'resend_otp_form\').submit();">Resend OTP ?</a></td>
						</tr>
						<tr><td colspan="3"></td></tr>
						<tr>
							<td>&nbsp;</td>
							<td style="width:17%">
								<input  type="submit" 
								        name="submit" 
								        value="Validate OTP" 
								        class="button button-primary button-large" />
							</td>
		</form>
		<form name="f" method="post">
						<td style="width:18%">
                            <input type="hidden" name="option" value="mo_idp_go_back"/>
                            <input type="submit" name="submit"  value="Back" class="button button-primary button-large" />
                        </td>
						</form>
							<form name="f" id="resend_otp_form" method="post" action="">
						<td>
							<input type="hidden" name="option" value="mo_idp_resend_otp"/>
						</td>
						</tr>			
		</form>
					</table>
		<br>
		<hr>

		<h3>I did not recieve any email with OTP . What should I do ?</h3>
		<form id="phone_verification" method="post" action="">
            <input type="hidden" name="option" value="mo_idp_phone_verification" />
			    If you cannot see an email from miniOrange in your mails, please check your <b>SPAM Folder</b>. 
			    If you don\'t see an email even in SPAM folder, verify your identity with our alternate method.
			    <br><br>
				<b>Enter your valid phone number here and verify your identity using one time passcode sent to your phone.</b>
				<br><br>
				<table class="mo_idp_settings_table">
					<tr>
					<td colspan="3">
						<input  class="mo_idp_table_textbox" 
						        required  
						        pattern="[0-9\+]{12,18}" 
						        autofocus="true" 
						        style="width:100%;" 
						        type="tel" 
						        name="phone_number" 
						        id="phone" 
						        placeholder="Enter Phone Number" 
						        value="'.get_site_option('mo_idp_admin_phone').'" 
						        title="Enter phone number(at least 10 digits) without any space or dashes."/>
					</td>
					<td>&nbsp;&nbsp;
						<a style="cursor:pointer;" onclick="document.getElementById(\'phone_verification\').submit();">Resend OTP ?</a>
					</td>
					</tr>
				</table>
				<br><input type="submit" value="Send OTP" class="button button-primary button-large" />

		</form>
            <br>
            <h3>What is an OTP ?</h3>
            <p>
                OTP is a one time passcode ( a series of numbers) that is sent to your email or phone number to verify 
                that you have access to your email account or phone. 
            </p>
		</div>';