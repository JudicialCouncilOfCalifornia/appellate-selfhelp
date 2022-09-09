<?php
    $pricing_column = '
        <table>
            <tr>
                <td class="mo_idp_pricing_text" style="width:35%;text-align:left;">Service Providers :</td>
                <td style="width:30%">
                    <select class="mo-form-control" id="noOfSp" required>{{options}}</select>
                </td>
                <td class="mo_idp_pricing_text">- One Time
            </tr>
            <tr>
                <td class="mo_idp_pricing_text" colspan="3">+<td>
            <tr>
                <td class="mo_idp_pricing_text" style="width:45%;text-align:left;">
                    SSO Users :
                    <span class="tooltip">
                          <i class="dashicons dashicons-warning"></i>
                          <span class="tooltiptext">
                              <span class="tooltip-header">WHAT DO YOU MEAN BY SSO USERS?</span>
                              <hr>
                              <span class="tooltip-body">
                                The users who perform sso are known as SSO users.<br/><br/> 
                                Example: If you have 1000 WordPress users but only 200 perform SSO then only need to pay
                                for 200 users and not the whole 1000 WordPress users.
                                For more than 5000 users contact us at  <a href="mailto:info@xecurify.com">info@xecurify.com</a>.
                              </span>
                          </span>
                    </div>
                </td>
                <td style="width:30%"> 
                    <select class="mo-form-control" id="noOfUsers">{{userOptions}}</select>
                </td>
                <td class="mo_idp_pricing_text">- One Time
            </tr>
        </table>';

        $pricing_column = str_replace('{{options}}',$options,$pricing_column);
        $pricing_column = str_replace('{{userOptions}}',$userOptions,$pricing_column);

	echo'<div class="mo_idp_divided_layout mo-idp-full">';
            is_customer_registered_idp($registered);
	echo'   <form style="display:none;" id="mocf_loginform" action="'.$login_url.'" target="_blank" method="post">
				<input type="email" name="username" value="'.$username.'" />
				<input type="text" name="redirectUrl" value="'.$payment_url.'" />
				<input type="text" name="requestOrigin" id="requestOrigin"  />
			</form>
            
            <div class="mo_idp_pricing_layout mo-idp-center">
                <table class="mo_idp_pricing_table">
                    <h2>LICENSING PLANS
                        <span style="float:right">
                            <input  type="button" 
                                    name="ok_btn" 
                                    id="ok_btn" 
                                    class="button button-primary button-large" 
                                    value="OK, Got It" 
                                    onclick="window.location.href=\''.$okgotit_url.'\'" />
                        </span>
                    <h2>
                    <hr>
                    <tr style="vertical-align:top;">
                        <td colspan="3">
                            <div class="mo_idp_thumbnail mo_idp_pricing_free_tab">
                                <h3 class="mo_idp_pricing_header">FREE</h3>
                                <br/><br/>
                                <hr>
                                <h4 class="mo_idp_pricing_sub_header">
                                    <div class="mo_idp_free_pricing-div">
                                    <span style="line-height: 100px">$0</span></div>
                                </h4>
                                <hr>
                                <p class="mo_idp_pricing_text">Features:</p>
                                <p class="mo_idp_pricing_text features">';
                                    foreach ($basic_features as $feature):
                                        echo $feature . '<br/>';
                                    endforeach;
            echo			'	</p>
                                <hr>
                                <p class="mo_idp_pricing_text">Basic Support By Email</p>
                            </div>
                        </td>
                        <td colspan="3">
                            <div class="mo_idp_thumbnail mo_idp_pricing_paid_tab">
                                <h3 class="mo_idp_pricing_header">DO IT YOURSELF</h3>
                                    <h4 class="mo_idp_pricing_sub_header">
                                        <input  id="freeUpgrade" 
                                                type="button" 
                                                style="margin-bottom:3.8%;" ' . $disabled . ' 
                                                class="button button-primary button-large" 
                                                onclick="mo2f_upgradeform(\'wp_saml_idp_basic_plan\')" 
                                                value="Click here to upgrade"/>*
                                    </h4>
                                <hr>
                                <h4 class="mo_idp_pricing_sub_header">
                                    <div class="mo_idp_pricing-div">
                                        '.$pricing_column.'
                                    </div>
                                </h4>
                                <hr>
                                <p class="mo_idp_pricing_text">Features:</p>
                                <p class="mo_idp_pricing_text features">';
                                    foreach ($premium_features as $feature):
                                        echo $feature . '<br/>';
                                    endforeach;
            echo			'	</p>
                                <hr>
                                <p class="mo_idp_pricing_text">Basic Support By Email</p>
                            </div>
                        </td>
                        <td colspan="3">
                        <div class="mo_idp_thumbnail mo_idp_pricing_paid_tab">
                                <h3 class="mo_idp_pricing_header">PREMIUM</h3>
                                <h4 class="mo_idp_pricing_sub_header">
                                    <input  type="button" 
                                            style="margin-bottom:3.8%;" ' . $disabled . ' 
                                            class="button button-primary button-large" 
                                            onclick="mo2f_upgradeform(\'wp_saml_idp_premium_plan\')" 
                                            value="Click here to upgrade"/>*
                                </h4>
                                <hr>
                                 <h4 class="mo_idp_pricing_sub_header">
                                    <div class="mo_idp_pricing-div">
                                        '.$pricing_column.'
                                    </div>
                                </h4>
                                <hr>
                                <p class="mo_idp_pricing_text">Features:</p>
                                <p class="mo_idp_pricing_text features">';
                                    foreach ($premium_features as $feature):
                                        echo $feature . '<br/>';
                                    endforeach;
                echo		'	</p><hr>
                                <p class="mo_idp_pricing_text">Premium Support Plans</p>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
            <div id="disclamer" class="mo_idp_pricing_layout mo-idp-center">
                <h3>* Steps to Upgrade to Premium Plugin -</h3>
                <p>
                    1. You will be redirected to miniOrange Login Console. 
                    Enter your password with which you created an account with us. 
                    After that you will be redirected to payment page.
                </p>
                <p>
                    2. Enter you card details and complete the payment. 
                    On successful payment completion, you will see the link to download the premium plugin.
                </p>
                <p>
                    3. Once you download the premium plugin, just unzip it and replace the folder with existing plugin. <br>
                    <b>Note: Do not first delete and upload again from wordpress admin panel as your already saved settings will get lost.</b></p>
                    <p>4. From this point on, do not update the plugin from the Wordpress store.</p>
                    <h3>** End to End Integration - </h3>
                    <p> 
                        We will setup a Conference Call / Gotomeeting and do end to end configuration for you. 
                        We provide services to do the configuration on your behalf. 
                    </p>
                    If you have any doubts regarding the licensing plans, you can mail us at 
                    <a href="mailto:info@xecurify.com"><i>info@xecurify.com</i></a> 
                    or submit a query using the <b>support form</b> on right.
                </p>
            </div>
            <div class="mo_idp_pricing_layout mo-idp-center">
                <h3>10 Days Return Policy</h3>
                <p>
                    At miniOrange, we want to ensure you are 100% happy with your purchase.  If the premium plugin you purchased is not working as
                    advertised and you have attempted to resolve any feature issues with our support team, which couldn\'t get resolved. We will
                    refund the whole amount within 10 days of the purchase. Please email us at
                    <a href="mailto:info@xecurify.com">info@xecurify.com</a> for any queries regarding the return policy.
                    <br> If you have any doubts regarding the licensing plans, you can mail us at 
                    <a href="mailto:info@xecurify.com">info@xecurify.com</a> or submit a query using the support form.
                </p>
                <br>
            </div>
        </div>';