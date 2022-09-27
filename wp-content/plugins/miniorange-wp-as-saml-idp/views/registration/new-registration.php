<?php
	echo'<!--Register with miniOrange-->
		<form name="f" method="post" action="" id="register-form">
			<input type="hidden" name="option" value="mo_idp_register_customer" />
			<div class="mo-idp-divided-layout mo-idp-full mo-idp-bg mo-idp-margin-left mo-idp-pt">
                <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width">
                    <h3 class="mo-idp-add-new-sp">Register With miniOrange</h3>
                    <hr class="mo-idp-add-new-sp-hr">
                        <div class="mo-idp-help-desc mo-idp-home-card-link mo-idp-sub-head-box">
                            All configurations made by you are stored on your WordPress instance and all transactions 
                            made are between your site and the Service Provider(s) that you have configured. 
                            <span class="mo-idp-red">We do not track any of your transactions or store any of your data.</span> 
                            For Premium plans, registration is mandatory so that you can better manage your licenses and this also allows us to respond to you when you need support.
                        </div>
                    <table class="mo-idp-settings-table mo-idp-mt-5" style="border-spacing:0 1.5rem">
    
                        <tr>
                        <td class="mo-idp-home-card-link"><b>Email<span class="mo-idp-red">*</span> :</b></td>                            
                        <td>';
echo '                            <input  class="mo-idp-table-textbox mo-idp-table-input"
                                        type="email" 
                                        name="email"
                                        required 
                                        placeholder="person@example.com";
                                        value="'.esc_attr($current_user->user_email).'" />';
echo '                            </td>
                        </tr>
                        <tr>
                            <td class="mo-idp-home-card-link"><b>Password<span class="mo-idp-red">*</span> :</b></td>
                            <td>
                                <input class="mo-idp-table-textbox mo-idp-table-input" required 
                                        type="password"
                                        name="password" 
                                        placeholder="Choose your password (Min. length 6)" />
                            </td>
                        </tr>
                        <tr>
                            <td class="mo-idp-home-card-link"><b>Confirm Password<span class="mo-idp-red">*</span> :</b><font></b></td>
                            <td>
                                <input  class="mo-idp-table-textbox mo-idp-table-input" required 
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
                                        class="button button-primary button-large mo-idp-button-large" />
                                <input  type="button"  
                                id="goToLoginPage"
                                style="font-size:1rem;color: #1F4476;font-weight: 600;width:19rem;margin-left:1%;height:42px;"
                                        value="Already have an account? Sign In" 
                                        class="button " />
                            </td>
                        </tr>
                    </table>
    
                </div>
               </div>
            </form>
            <form name="goToLoginPage" method="post" action="" id="goToLoginPageForm">';
                wp_nonce_field($regnonce);
           echo '     <input type="hidden" name="option" value="remove_idp_account"/>
            </form>';