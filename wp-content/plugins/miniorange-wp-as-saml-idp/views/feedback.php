<?php

echo '
			<!--------------------------------- NEW CODE ------------------------------------------>
			<!-- The Modal -->
			<div id="mo-idp-feedback-modal" class="mo-idp-modal">
			    <!-- Modal content -->
			    <div class="mo-idp-modal-content">
                <span class="mo-idp-feedback-close">&times;</span>
                <h3 class="mo-idp-text-center">Customer Feedback </h3>
                    <form id="mo-idp-feedback-form" name="f" method="post" action="">';
                  echo'  <div class="mo-idp-keep-conf-intact" id="mo-idp-keep-configuration-intact">
                        <b style="color:red;">'.__('Keep Configuration Intact').'</b>
                        <label class="mo-idp-switch" style="float:right">
                            <input type="checkbox" name="idp_keep_settings_intact" id="keepSettingsIntact" '.($keepSettingsIntact ? 'checked' : '').'>
                            <span class="mo-idp-slider round"></span>
                        </label>
                        <br>
                        <br>
                        <p>'.__('Enabling this would keep your settings intact when plugin is uninstalled.').'
                        ' . __('Please enable this option when you are updating to a Premium version.') . '    
                        </p>
                    </div>';
                    echo'<br>
                            <!--  overall padding -->
                            <div class="mo-idp-padd">
                                <hr style="width:75%;"/>
                                <h4> Please help us to improve our plugin by giving your opinion. </h4>
                                    <!--  Rating Icon Div -->
                                    <div id="mo-idp-smi-rate" class="mo-idp-radio-image">
                                        <input type="radio" name="idp_rate" id="idp_angry" value="1"/>
                                        <label for="idp_angry"><img width="30" height="30" src="';
                                        echo MSI_URL . '/includes/images/angry.png';
                                        echo '" /></label>
                                        <input type="radio" name="idp_rate" id="idp_sad" value="2"/>
                                        <label for="idp_sad"><img width="30" height="30" src="';
                                        echo MSI_URL . '/includes/images/sad.png';
                                        echo '"/></label>
                                        <input type="radio" name="idp_rate" id="idp_neutral" value="3"/>
                                        <label for="idp_neutral"><img width="30" height="30" src="';
                                        echo MSI_URL . '/includes/images/normal.png';
                                        echo '"/></label>
                                        <input type="radio" name="idp_rate" id="idp_smile" value="4"/>
                                        <label for="idp_smile"><img width="30" height="30" src="';
                                        echo MSI_URL . '/includes/images/smile.png';
                                        echo '"/></label>
                                        <input type="radio" name="idp_rate" id="idp_happy" value="5" checked/>
                                        <label for="idp_happy"><img width="30" height="30" src="';
                                        echo MSI_URL . '/includes/images/happy.png';
                                        echo '"/></label>
                                        <br>
                                        <div id="idp_outer" style="visibility:visible;margin-top:0.438rem;margin-bottom:1.125rem;">
                                            <span id="IDPresult">Thank you for appreciating our work <span>
                                        </div>
                                    </div>
                                <hr style="width:75%;"/>
                                    <!--  Text Area, Customer message -->
                                    <textarea style="margin-top:10px;font-size:14px;" id="idp_query_feedback" name="idp_query_feedback" rows="4" cols="40" placeholder="Tell us what happened!"></textarea>
                                    <!-- <hr/>  -->
                                    <br>
                                ';

$user = wp_get_current_user();
$email = $user->user_email;

echo '
                                    <!--  Customer Email Id Display -->
                                    <div class="mo-idp-radio-email" align="center">
                                        <input type="email" id="idp_email" name="idp_email" placeholder="your email address" style="background:#f0f3f7;border-style: none;width:20vw; text-align:center;" required value="';
echo esc_attr($email);
echo '" readonly="readonly"/>
                                        <input type="radio" name="idp_edit" id="idp_edit" onclick="editIDPName()" value=""/>
                                            <label for="idp_edit"><img width="15" height="15" src="';
                                            echo MSI_URL . '/includes/images/61456.png';
                                            echo '"/>
                                        </label>
                                   </div>
                                   <!--  MiniOrange Email CheckBox  -->
                                    <p align="center">
                                        <input type="checkbox" id="idp_dnd" name="idp_dnd" value="allow" checked>
                                        <label for="idp_dnd"> miniOrange representative will reach out to you at the email-address entered above</label>
                                    </p>

                            </div>  <!--  Padd close -->

            <!----------------------------- NEW CODE END ----------------------------------------->

                                    <div class="mo_idp_feedback_form_div">
                                        <input type="hidden" name="option" value="mo_idp_feedback_option"/>
                                        <input type="hidden" value="false" id="feedback_type" name="plugin_deactivated"/>';

wp_nonce_field($nonce);

echo '
                                    </div>
                                    <!-- Submit / Skip Button -->
                                    <div align="center" style="padding:0.25rem;margin-bottom:0.625rem;">
                                        <input type="submit"
                                                style="margin-right:0.938rem;margin-left:0.938rem;font-size:0.875rem;"
                                                name="miniorange_feedback_submit"
                                                class="button button-primary button-large"
                                                value="Submit Feedback" />
                                        <input type="submit"
                                                style="margin-left:0.938rem;font-size:0.875rem;"
                                                id="mo_skip_and_deactivate"
                                                name="miniorange_feedback_submit"
                                                class="button button-primary button-large"
                                                value="Skip & Deactivate" />
                                    </div>
                    </form>
			    </div>  <!-- modal-content close -->
			</div>      <!-- modal close -->


<script>

var idp_modal = document.getElementById("mo-idp-feedback-modal");

var idp_span = document.getElementsByClassName("mo-idp-feedback-close")[0];



// When the user clicks on <span> (x), close the modal
idp_span.onclick = function()
{
    idp_modal.style.display = "none";
}

function editIDPName()
{
    document.querySelector("#idp_email").removeAttribute("readonly");
    document.querySelector("#idp_email").focus();
    return false;
}

const IdpINPUTS = document.querySelectorAll("#mo-idp-smi-rate input");
IdpINPUTS.forEach(element => element.addEventListener("click", (elm) => updateIDPValue(elm)));

function updateIDPValue(elm)
{
    document.querySelector("#idp_outer").style.visibility="visible";
    var idp_result = "Thank you for appreciating our work";

            switch(elm.target.value)
            {
                            case "1":	idp_result = "Not happy with our plugin ? Let us know what went wrong";
                                break;
                            case "2":	idp_result = "Found any issues? Let us know and we\'ll fix it ASAP";
                                break;
                            case "3":	idp_result = "Let us know if you need any help";
                                break;
                            case "4":	idp_result = "We\'re glad that you are happy with our plugin";
                                break;
                            case "5":	idp_result = "Thank you for appreciating our work";
                                break;
            }
    document.querySelector("#IDPresult").innerHTML = idp_result;
}
</script>
';
