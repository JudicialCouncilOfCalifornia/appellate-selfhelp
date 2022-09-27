<?php

echo '<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_idp_connect_verify_customer" />
			<div class="mo-idp-divided-layout mo-idp-full mo-idp-bg mo-idp-margin-left mo-idp-pt">
			    <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width">
                    <h2 class="mo-idp-add-new-sp">
                       Login With miniOrange
                    </h2>
                    <hr class="mo-idp-add-new-sp-hr"/>
                    <p class="mo-idp-text-center mo-idp-home-card-link mo-idp-sub-head-box" >
                        <b>
                            It seems you already have an account with <span class="mo-idp-red">miniOrange</span>. 
                            Please enter your <span class="mo-idp-red">miniOrange email</span> and <span class="mo-idp-red">password</span>. 
                        </b>
                    </p>
                    <table class="mo-idp-settings-table mo-idp-sp-data-table" style="border-spacing:0 1.5rem">
                        <tr>
                            <td class="mo-idp-home-card-link"><b>Email<span class="mo-idp-red">*</span> :</b></td>
                            <td>';
                            echo  '  <input  class="mo-idp-table-textbox mo-idp-table-input" 
                                        type="email" 
                                        name="email"
                                        required 
                                        placeholder="person@example.com"
                                        value="'. esc_attr($email) .'"/>';
                    echo '  </td>
                        </tr>
                        <tr>
                            <td class="mo-idp-home-card-link"><b>Password<span class="mo-idp-red">*</span> :</b></td>
                            <td>
                                <input  class="mo-idp-table-textbox mo-idp-table-input" required 
                                        type="password"
                                        name="password" 
                                        placeholder="Choose your password" />
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <span style="position: relative">
                                    <input  type="submit" 
                                            name="submit"
                                            class="button button-primary button-large mo-idp-button-large " 
                                            value="Submit" />
                                </span>
                                <span style="position: relative;margin-left:1%;">
                                    <input  type="button"  
                                            id="goBackButton" 
                                            style="font-size: 1rem;color: #1F4476;font-weight: 600;width:19rem;height:42px;"
                                            value="Go Back to Registration Form"
                                            class="button" />
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <span style="margin-top:-10px;">
                                <input  type="button" 
                                        name="forgot_password" 
                                        id="forgot_pass" 
                                        class="button" style="font-size: 1.05rem;
                                        background: white;
                                        color: #1F4476;
                                        border: none;
                                        text-decoration: underline;
                                        margin-left:5rem;
                                        "; 
                                        value="Click here if you forgot your password">
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
			</div>
		</form>
		<form name="goBack" method="post" action="" id="goBacktoRegistrationPage">';
		    wp_nonce_field($regnonce);
		echo'<input type="hidden" name="option" value="mo_idp_go_back"/>
		</form>
		<form name="forgotpassword" method="post" action="" id="forgotpasswordform">';
		    wp_nonce_field($regnonce);
			echo '<input type="hidden" name="option" value="mo_idp_forgot_password"/>
		</form>
		<script>
			jQuery(\'#forgot_pass\').click(function(){
				jQuery("#forgotpasswordform").submit();
			});
			jQuery("#goBackButton").click(function(){
				jQuery("#goBacktoRegistrationPage").submit();
			});
		</script>';