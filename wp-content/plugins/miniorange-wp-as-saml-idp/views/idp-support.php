<?php

	echo '
	<div class="mo-idp-divided-layout mo-idp-full mo-idp-bg mo-idp-margin-left mo-idp-pt">
        <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width">
            <h2 class="mo-idp-add-new-sp">SUPPORT</h2><hr class="mo-idp-add-new-sp-hr">
            <p class="mo-idp-text-center mo-idp-home-card-link" style="font-weight: 600;background: #D5E2FF;padding: 1.5rem;border-radius: 5px;">Need any help? Send us a query so we can help you.</p>
            <form method="post" action="">
                <input type="hidden" name="option" value="mo_idp_contact_us_query_option" />
                <table class="mo-idp-settings-table" style="border-spacing:0 1rem">
                    <tr>
                        <td class="mo-idp-home-card-link" width="20%"><b>Email:</b></td>
                        <td>';
                           echo' <input  type="email" 
                                    class="mo-idp-table-input" required 
                                    placeholder="Enter your Email" 
                                    name="mo_idp_contact_us_email" 
                                    value="'.esc_attr($email).'">';
                       echo' </td>
                    </tr>
                    <tr>
                         <td class="mo-idp-home-card-link" width="20%"><b>Phone:</b></td>
                         <td>';
                           echo' <input  type="tel" 
                                    id="contact_us_phone" 
                                    pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" 
                                    placeholder="Enter your phone number with country code (+1)" 
                                    class="mo-idp-table-input" 
                                    name="mo_idp_contact_us_phone" 
                                    value="'.esc_attr($phone).'">';
                       echo' </td>
                    </tr>';
    if (!empty($plan))
    {
        echo '      <tr>
                        <td  style="font-weight:600;width:20%;">
                            <label class="mo-idp-home-card-link" for="plan-name-dd">Choose a plan:</label>
                        </td>
                        <td style="width:20%;">    
                            <select name="mo_idp_upgrade_plan_name" class="mo-idp-table-input mo-idp-select" id="plan-name-dd">';
                               echo' <option value="lite_monthly"
                                '.(!empty($plan) && strpos($plan,'lite_monthly') ? 'selected' : '').'>
                                    Cloud IDP Lite - Monthly Plan
                                </option>';
                               echo'  <option value="lite_yearly"
                                '.(!empty($plan) && strpos($plan,'lite_yearly') ? 'selected' : '').'>
                                    Cloud IDP Lite - Yearly Plan
                                </option>';
                               echo' <option value="wp_yearly"
                                '.(!empty($plan) && strpos($plan,'wp_yearly') ? 'selected' : '').'>
                                    WordPress Premium - Yearly Plan
                                </option>';
                               echo' <option value="all_inclusive"
                                '.(!empty($plan) && strpos($plan,'all_inclusive') ? 'selected' : '').'>
                                    All Inclusive Plan
                                </option>';
                           echo' </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 20%;font-weight:600" class="mo-idp-home-card-link">
                            Number of users: 
                        </td>
                        <td class="mo-idp-table-contact mo-idp-table-textbox">   '; 
                          echo'  <input  type="text"
                                    class="mo-idp-table-input"
                                    name="mo_idp_upgrade_plan_users"
                                    placeholder = "Enter the number of users"
                                    value="'.(!empty($users)? esc_attr($users) : '').'">';
                      echo'  </td>
                    </tr>';
    }
    echo '          <tr>
                        <td class="mo-idp-home-card-link" width="20%"><b>Description:</b></td>
                        <td>';
                           echo' <textarea   class="mo-idp-table-contact mo-idp-table-textbox mo-idp-table-input" 
                                        onkeypress="mo_idp_valid_query(this)" 
                                        onkeyup="mo_idp_valid_query(this)" 
                                        placeholder="Write your query here" 
                                        onblur="mo_idp_valid_query(this)" required 
                                        name="mo_idp_contact_us_query" 
                                        rows="4" 
                                        style="resize: vertical;">'.esc_textarea($request_quote).'</textarea>';
                      echo'  </td>
                    </tr>
                </table>
                <br>
                <div class="mo-idp-flex">
                    <input  type="submit" 
                            name="submit" 
                            value="Submit Query" 
                            style="width:110px;" 
                            class="button button-primary button-large mo-idp-button-large" />
                </div>
            </form>
            <p class="mo-idp-text-center mo-idp-home-card-link">
                If you want custom features in the plugin, reach out to us at 
                <a href="mailto:samlsupport@xecurify.com">samlsupport@xecurify.com</a>.
            </p>
        </div>
    </div>
    
        <script>
            function moSharingSizeValidate(e){
                var t=parseInt(e.value.trim());t>60?e.value=60:10>t&&(e.value=10)
            }
            function moSharingSpaceValidate(e){
                var t=parseInt(e.value.trim());t>50?e.value=50:0>t&&(e.value=0)
            }
            function moLoginSizeValidate(e){
                var t=parseInt(e.value.trim());t>60?e.value=60:20>t&&(e.value=20)
            }
            function moLoginSpaceValidate(e){
                var t=parseInt(e.value.trim());t>60?e.value=60:0>t&&(e.value=0)
            }
            function moLoginWidthValidate(e){
                var t=parseInt(e.value.trim());t>1000?e.value=1000:140>t&&(e.value=140)
            }
            function moLoginHeightValidate(e){
                var t=parseInt(e.value.trim());t>50?e.value=50:35>t&&(e.value=35)
            }
        </script>';