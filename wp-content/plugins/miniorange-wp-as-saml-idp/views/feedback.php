<?php

echo '

            <div class="mo_idp_backdrop">
                <div class="mo_idp_modal" tabindex="-1" role="dialog" id="mo_idp_feedback_modal">
                    <div class="mo_idp_modal_backdrop">
                    </div>
                    <div class="mo_idp_modal_dialog mo_idp_modal_md idp_feedback_modal">
                        <div class="login mo_idp_modal_content">
                            <div class="mo_idp_modal_header">
                                <b>FEEDBACK</b>
                                <a class="close" href="#" onclick="mo_idp_feedback_goback()">&larr; Go Back</a>
                            </div>
                            <form id="mo_idp_feedback_form" name="f" method="post" action="">
                                 <div class="mo_idp-modal-body">
                                    <div class="mo_idp_note mo_feedback_note"><i>'.$message.'</i></div>
                                    <br>
                                    <div class="mo_idp_feedback_form_div">
                                        <input type="hidden" name="option" value="mo_idp_feedback_option"/>
                                        <input type="hidden" value="false" id="feedback_type" name="plugin_deactivated"/>';

                                        wp_nonce_field($nonce);

echo'                                   
                                        <textarea   id="query_feedback" 
                                                    name="query_feedback" 
                                                    style="width:100%" 
                                                    rows="4" 
                                                    placeholder="Type your feedback here"></textarea>
                                    </div>
                                </div>
                                <div class="mo_idp_modal_footer" >
                                    <input  type="button" 
                                            id="mo_idp_feedback_cancel_btn" 
                                            class="button button-primary button-large" 
                                            onclick="mo_idp_feedback_goback()" 
                                            value="&larr; Go Back"/>
                                    <input  type="submit" 
                                            name="miniorange_feedback_submit" 
                                            class="button button-primary button-large" 
                                            value="Submit Feedback" />
                                    <input  type="submit" 
                                            id="mo_skip_and_deactivate" 
                                            name="miniorange_feedback_submit" 
                                            class="button button-primary button-large" 
                                            value="Skip & Deactivate" />
                                </div>
                            </form>    
                        </div>
                    </div>
                </div>
            </div>';