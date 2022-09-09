<?php

	echo'<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_idp_connect_verify_customer" />
			<div class="mo_idp_divided_layout mo-idp-full">
			    <div class="mo_idp_table_layout mo-idp-center">
                    <h2>
                       LOGIN WITH MINIORANGE
                        <span style="float:right;margin-top:-10px;">
                            <input  type="button" 
                                    name="forgot_password" 
                                    id="forgot_pass" 
                                    class="button button-primary button-large" 
                                    value="Forgot Password?">
                        </span>
                    </h2>
                    <hr/>
                    <p>
                        <b>
                            It seems you already have an account with miniOrange. 
                            Please enter your miniOrange email and password. 
                        </b>
                    </p>
                    <table class="mo_idp_settings_table">
                        <tr>
                            <td><b><font color="#FF0000">*</font>Email:</b></td>
                            <td>
                                <input  class="mo_idp_table_textbox" 
                                        type="email" 
                                        name="email"
                                        required 
                                        placeholder="person@example.com"
                                        value="'. $email .'"/>
                            </td>
                        </tr>
                        <tr>
                            <td><b><font color="#FF0000">*</font>Password:</b></td>
                            <td>
                                <input  class="mo_idp_table_textbox" required 
                                        type="password"
                                        name="password" 
                                        placeholder="Choose your password" />
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <input  type="submit" 
                                        name="submit"
                                        class="button button-primary button-large" 
                                        value="Submit" />
                                <input  type="button" 
                                        id="goBackButton" 
                                        value="Go Back to Registration Form"
                                        class="button button-primary button-large" />
                            </td>
                        </tr>
                    </table>
                </div>
			</div>
		</form>
		<form name="goBack" method="post" action="" id="goBacktoRegistrationPage">
		    '.wp_nonce_field($regnonce).'
			<input type="hidden" name="option" value="mo_idp_go_back"/>
		</form>
		<form name="forgotpassword" method="post" action="" id="forgotpasswordform">
		    '.wp_nonce_field($regnonce).'
			<input type="hidden" name="option" value="mo_idp_forgot_password"/>
		</form>
		<script>
			jQuery(\'#forgot_pass\').click(function(){
				jQuery("#forgotpasswordform").submit();
			});
			jQuery("#goBackButton").click(function(){
				jQuery("#goBacktoRegistrationPage").submit();
			});
		</script>';