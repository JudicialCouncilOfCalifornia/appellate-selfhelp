<?php
	echo'<!--Register with miniOrange-->
		<form name="f" method="post" action="" id="register-form">
			<input type="hidden" name="option" value="mo_idp_register_customer" />
			<div class="mo_idp_divided_layout mo-idp-full">
                <div class="mo_idp_table_layout mo-idp-center">';
        echo		'<h3>REGISTER WITH MINIORANGE</h3>
                    <hr>
                    <p>
                        <!--<div class="mo_idp_help_title">Why should I register?</a></div>-->
                        <div class="mo_idp_help_desc">
                            All configurations made by you are stored on your WordPress instance and all transactions 
                            made are between your site and the Service Provider(s) that you have configured. 
                            We do not track any of your transactions or store any of your data. 
                            We have made registration mandatory for upgrades so that you can better manage your licenses 
                            and allows us to get back to you as in when you need support.
                        </div>
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
                                        value="'.$current_user->user_email.'" />
                            </td>
                        </tr>
                        <tr>
                            <td><b><font color="#FF0000">*</font>Password:</b></td>
                            <td>
                                <input class="mo_idp_table_textbox" required 
                                        type="password"
                                        name="password" 
                                        placeholder="Choose your password (Min. length 6)" />
                            </td>
                        </tr>
                        <tr>
                            <td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
                            <td>
                                <input  class="mo_idp_table_textbox" required 
                                        type="password"
                                        name="confirmPassword" 
                                        placeholder="Confirm your password" />
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <br />
                                <input  type="submit" 
                                        name="submit" 
                                        value="Register" 
                                        class="button button-primary button-large" />
                                <input  type="button"  
                                        id="goToLoginPage"
                                        value="Already have an account? Sign In" 
                                        class="button button-primary button-large" />
                            </td>
                        </tr>
                    </table>
    
                </div>
               </div>
            </form>
            <form name="goToLoginPage" method="post" action="" id="goToLoginPageForm">
                '.wp_nonce_field($regnonce).'
                <input type="hidden" name="option" value="remove_idp_account"/>
            </form>';