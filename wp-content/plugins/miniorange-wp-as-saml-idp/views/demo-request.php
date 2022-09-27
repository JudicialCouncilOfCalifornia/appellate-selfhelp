<?php

    echo '
        <form name="demo_request" method="post" action="" id="demorequest">
        <input type="hidden" name="option" value="mo_idp_request_demo">';
        wp_nonce_field($demononce);
        echo'<div class="mo-idp-divided-layout mo-idp-full mo-idp-bg mo-idp-margin-left mo-idp-pt">
        <div class= "mo-idp-table-layout mo-idp-center mo-idp-sp-width">
                <h3 class="mo-idp-add-new-sp">Request a Demo</h3>
                <hr class="mo-idp-add-new-sp-hr">
                <p class="mo-idp-text-center mo-idp-home-card-link mo-idp-sub-head-box" >
                    Want to try out the paid features before purchasing the license? Let us know about your requirements, and we will set up a demo for you.
                </p>
            <div class="mo-idp-demo-table mo-idp-flex" style="justify-content:space-around;align-items:start">
               <div class="mo-idp-demo-table1"> 
                <table class="mo-idp-settings-table" style="border-spacing:0.313rem 1.5rem;">
                    <tr>
                        <td class="mo-idp-home-card-link"><b>Plugin :</b></td>
                        <td class="mo-idp-home-card-link"><b>WordPress IDP Premium Plugin</b></td>
                    </tr>
                    <tr>
                        <td class="mo-idp-home-card-link"><b>Email<font color="#FF0000">*</font> :</b></td>
                        <td>';
                            echo'<input class="mo-idp-table-textbox mo-idp-table-input"
                                        type="email"
                                        name="mo_idp_demo_email"
                                        required 
                                        placeholder="We will use this email to setup the demo for you"
                                        value="'.esc_attr($mo_idp_demo_email).'"/>';
                       echo' 
                        </td>
                    </tr>
                    <tr>
                        <td class="mo-idp-home-card-link"><b>Description :<b></td>
                        <td>
                            <textarea rows="4" class="mo-idp-table-textbox mo-idp-table-input" name="mo_idp_demo_description" required placeholder="Write us about your requirement" value=""></textarea>    
                        </td>
                    </tr>
                    <tr class="mo-idp-text-center">
                        <td colspan="2" >
                            <input type="submit" value="Request a Demo" class="button button-primary button-large mo-idp-button-large"/>
                        </td>
                    </tr>
                  </table>
                </div> 
                <div class="mo-idp-demo-table2">
                    <h3 class="mo-idp-advt-premium-feature-head-jwt-prem">What will you get?</h3>
                    <p class="mo-idp-demo-form-desc">In this demo you will get access to all the features like:</p>
                    <div class="mo-idp-metadata mo-idp-m-0" >
                        <span class="dashicons dashicons-arrow-right-alt"></span>
                        <p class="mo-idp-home-card-link mo-idp-rqst-demo-get">Attribute Mapping</p>
                    </div>
                    <div class="mo-idp-metadata mo-idp-m-0">
                        <span class="dashicons dashicons-arrow-right-alt"></span>
                        <p class="mo-idp-home-card-link mo-idp-rqst-demo-get" >Multiple SPs Supported</p>
                    </div>
                    <div class="mo-idp-metadata mo-idp-m-0" >
                        <span class="dashicons dashicons-arrow-right-alt"></span>
                        <p class="mo-idp-home-card-link mo-idp-rqst-demo-get" >Group & Role Mapping</p>
                    </div>
                    <div class="mo-idp-metadata mo-idp-m-0" >
                        <span class="dashicons dashicons-arrow-right-alt"></span>
                        <p class="mo-idp-home-card-link mo-idp-rqst-demo-get">Role Based SSO & much more</p>
                    </div>
                    <br>';
                    echo'<span style="font-size:1rem;font-weight: 600;margin-left: 2rem;">For more details, <a href="'.esc_url($license_url).'">click here &#8594</a></span>';
               echo' </div>
                <!--end of table2-->
            </div> <!--end of flex div-->
                </div>
            </div>
        </form>
    
    ';



