<?php
echo 	'<div class="mo-idp-divided-layout mo-idp-full mo-idp-bg mo-idp-margin-left mo-idp-pt">';
able_to_write_files($registered,$verified);
echo    '<div id="mo-idp-successDiv" style="display:none; width:64%; margin:auto; margin-top:0.625rem; color:black; background-color:rgba(44, 252, 2, 0.15); 
            padding:0.938rem; border:solid 1px rgba(55, 242, 07, 0.36); font-size:large; line-height:normal">
                <span style="color:green;">
                    <span class="dashicons dashicons-yes-alt"></span> 
                    <b>SUCCESS</b>:
                </span> 
                You have successfully updated your certificates.
        </div>
        <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width ">
                        <h2 class="mo-idp-add-new-sp">
                            IDP Metadata
                        </h2>
                        <hr class="mo-idp-add-new-sp-hr">
                        <div class="mo-idp-mt-5">
                            <p class="mo-idp-home-card-link"><b>You will need the following information to configure your Service Provider. Copy it and keep it handy.</b></p>
                        </div>
                        <div class="mo-idp-metadata">
                            <div class="mo-idp-home-card-link"><b>Metadata URL: </b></div>';
                            echo'<div id="metadataXML" class="mo-idp-note mo-idp-metadata-note" style="width:43%;padding:0.875rem;margin-left:6.5rem">
                              <b><a class="mo-idp-home-card-link" id="idp_metadata_url" target="_blank" href="'.esc_url($metadata_url).'">'.esc_url($metadata_url).'</a></b>  
                            </div>';
                           echo' <span class="dashicons dashicons-admin-page mo-idp-copytooltip" style="float:right" onclick="copyToClipboard(this, \'#idp_metadata_url\', \'#idp_metadata_url_copy\');"><span id="idp_metadata_url_copy" class="mo-idp-copytooltiptext">Copy to Clipboard</span></span>
                        </div>
                        <div class="mo-idp-metadata">
                            <div class="mo-idp-home-card-link"><b>Metadata XML File:</b></div>';
                           echo' <a class="mo-idp-btn-cstm" href="'.esc_url($metadata_url).'" download="mo-idp-metadata.xml">Download <img style="width: 1.5rem;
                            filter: invert(1);
                            position: relative;
                            top: 0.3rem;" src="'.MSI_URL.'includes/images/download-icon.png"/></a>';
                       echo' </div>        
                        <div class="mo-idp-text-center mo-idp-or-block" >
                            <div class="mo-idp-mt-5" >
                                <hr class="mo-idp-add-new-sp-hr" style="border-top:3px solid #6c757d!important;width:15rem;">
                                <span class="mo-idp-metadata-or mo-idp-bg-secondary mo-idp-rounded-circle mo-idp-text-white">OR</span>
                            </div>
                        </div>
                        <table  style="border:1px solid #CCCCCC;margin-top:2.5rem!important ; border-collapse: collapse;" class="mo-idp-settings-table" id = "mo-idp-idpInfoTable">';
                         echo'   <tr>
                                <td style="width:40%" class="mo-idp-p-3 mo-idp-home-card-link"><b>IDP-EntityID / Issuer</b></td>
                               <td style="justify-content:space-between;" class="mo-idp-flex mo-idp-p-3 mo-idp-home-card-link"><span id="idp_entity_id">'.esc_attr($idp_entity_id).'</span>
                               <span class="dashicons dashicons-admin-page mo-idp-copytooltip" style="float:right;" onclick="copyToClipboard(this, \'#idp_entity_id\', \'#idp_entity_id_copy\');"><span id="idp_entity_id_copy" style="position: absolute;top: 2.5rem;left: -3rem;" class="mo-idp-copytooltiptext" >Copy to Clipboard</span></span></td>
                            </tr>';
                            echo'<tr>
                                <td style="width:40%" class="mo-idp-p-3 mo-idp-home-card-link"><b>SAML Login URL / Passive Login URL</b></td>
                                <td style="justify-content:space-between" class="mo-idp-flex mo-idp-p-3 mo-idp-home-card-link"><span id="saml_login_url">'.esc_url($site_url).'</span>
                                <span class="dashicons dashicons-admin-page mo-idp-copytooltip" style="float:right" onclick="copyToClipboard(this, \'#saml_login_url\', \'#saml_login_url_copy\');"><span id="saml_login_url_copy" style="position: absolute;top: 2.5rem;left: -3rem;"  class="mo-idp-copytooltiptext">Copy to Clipboard</span></span></td>
                            </tr>';
                           echo' <tr>
                                <td style="width:40%" class="mo-idp-p-3 mo-idp-home-card-link"><b>SAML Logout URL / WS-FED Logout URL</b></td>
                                <td class="mo-idp-p-3 mo-idp-home-card-link">
                                <div class="mo-idp-table-btn-cstm mo-idp-metadata-prem-feature-btn">
                                    <a  href="'.esc_url($license_url).'" class="mo-idp-upload-data-anchor" style="color:orange;"><img src="'.MSI_URL.'includes/images/star.png" style="width:1rem;">
                                    Premium Feature   
                                </div>
                                </td>
                            </tr>';
                           echo' <tr>
                                <td style="width:40%" class="mo-idp-p-3 mo-idp-home-card-link"><b>Certificate (Optional)</b></td>';
                                if ($expired_cert) 
                                  echo'<td class="mo-idp-p-3 mo-idp-home-card-link">
                                  <div class="mo-idp-table-btn-cstm" >
                                    <a class="mo-idp-table-btn-cstm" href="'.esc_url($certificate_url).'" >Download <img style="width: 1.5rem;
                                    filter: invert(1);
                                    position: relative;
                                    top: 0.3rem;" src="'.MSI_URL.'includes/images/download-icon.png"/></a>
                                  </div>
                                 </td>'; 

                                if (!$expired_cert) 
                                echo'<td style="width:60%;  padding: 1.6rem 1rem;">
                                    <div class="mo-idp-note" style="border-width:0.625rem; border-color: red;">
                                        <form name="f" method="post" action="" id="mo_idp_new_cert_form">
                                            <input type="hidden" name="option" value="mo_idp_use_new_cert" /></form>
                                        <span class="mo-idp-red">The existing certificates have expired.</span>
                                        <br>
                                        Download the latest certificate from <a href="'.esc_url($new_certificate_url).'" download>here</a>.
                                        <br>
                                        After updating your Service Provider, 
                                        <br>
                                        Click <button id="myBtn" style="background: orange; text-decoration: none; cursor: pointer; text-shadow: none; border-width: 2px; border-color: red; border-style: solid; "><b>HERE</b></button> to update the certificates in the plugin.
                                    </div>';
                                echo'</td>
                            </tr>
                            <tr>
                                <td style="width:40%" class="mo-idp-p-3 mo-idp-home-card-link"><b>Response Signed</b></td>
                                <td class="mo-idp-p-3 mo-idp-home-card-link"><div class="mo-idp-table-btn-cstm mo-idp-metadata-prem-feature-btn">';
                              echo'  <a  href="'.esc_url($license_url).'" style="color:orange;" class="mo-idp-upload-data-anchor"><img src="'.MSI_URL.'includes/images/star.png" style="width:1rem;">';
                              echo'  Premium Feature   
                                </div>
                                </td>
                            </tr>
                        </table>
                        <form name="f" method="post" action="" id="mo_idp_settings" class="mo-idp-form-border mo-idp-mt-5">
                            <div>
                                <h4 class="mo-idp-form-head mo-idp-entity-info mo-idp-mt-0">
                                    Identity Provider Endpoints
                                </h4>
                            </div>
                            <input type="hidden" name="option" value="mo_idp_entity_id" />
                            <table>
                                <tr>
                                    <td style="width:20%;vertical-align:initial;" class="mo-idp-home-card-link"><b>IdP EntityID / Issuer:</b></td>
                                    <td></td><td></td><td></td><td></td>
                                    <td>';
                                     echo'   <input  type="text" 
                                                name="mo_saml_idp_entity_id" 
                                                placeholder="Entity ID of your IdP" 
                                                style="width: 100%;padding:0.313rem;" 
                                                value="'.esc_attr($idp_entity_id).'"
                                                required="">';
                                    echo'    <div class="mo-idp-note-endp">
                                                <b>
                                                    <span class="mo-idp-red"><b>Note:</b></span> 
                                                    If you have already shared the above URLs or Metadata with your SP, 
                                                    do <b>NOT</b> change IdP EntityID. It might break your existing login flow.
                                                </b>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div class="mo-idp-flex mo-idp-mt-5">
                                <input  type="submit" 
                                    name="submit" 
                                    style="width:6.25rem;" 
                                    value="Update" 
                                    class="button button-primary button-large mo-idp-btn-cstm ">
                            </div>
                        </form>                
            </div>
            <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width" style="width:90.2%">
                <h4 class="mo-idp-home-card-link">You can run the following command to set WS-FED as your Authentication Protocol for your Federated Domain:</h4>
                <div class="mo-idp-note-endp">
                    <b><span class="mo-idp-red"><b>Note:</b></span> Make sure to replace `&lt;your_domain&gt;` with your domain before running the command.</b> 
                </div>
                <div class="mo-idp-copyClip" style="float:right;position: absolute;right: 3rem;bottom: 4rem;"> 
                    <h4><span class="dashicons dashicons-admin-page mo-idp-copytooltip" style="background: #1F4476;color: white;" > </span> </h4> 
                </div>';
               echo' <textarea style="width:100%;height:100px;font-family:monospace;padding: 0.3rem 3rem 0.5rem 0.5rem;" class="copyBody mo-idp-sp-data-table">'.esc_textarea($wsfed_command).'</textarea>';
           echo' </div>
          </div>
<!-- The Modal -->
<div id="myModal" class="modal">

    <!-- Modal content -->
    <div class="modal-content">

        <span class="modal-close-x">&times;</span>

        <div class="modal-header">
            <h1 class="modal-title">CAUTION</h1>
        </div>

        <div class="modal-body">
            <p id="isSPupdated" style="font-size:large; line-height:normal; display:block;">Certificate mismatch will <span class="mo-idp-red"><b>BREAK</b></span> your current SSO! <br>Did you update your Service Provider with the latest metadata XML/certificate?</p>
            <p id="confirmation" style="font-size:large; line-height:normal; display:none;">Click on the <span style="color:#0071a1;"><b>Confirm</b></span> button to update the certificates in the plugin.</p> 
            <p id="downloadMsg" style="font-size:large; line-height:normal; display:none;">Please update your Service Provider with the latest metadata XML / certificate.</p>
        </div>

        <div class="modal-footer">
            <button type="button" id="q1yes" style="margin-right:5%; display:inline-block;" class="button button-primary button-large modal-button">Yes</button>
            <button type="button" id="q1no" class="button button-primary button-large modal-button" style="display:inline-block;">No</button>
            <button type="button" id="confirmUpdate" style="margin-right:5%; display:none;" class="button button-primary button-large modal-button">Confirm</button>
            <button type="button" id="goBack" class="button button-primary button-large modal-button" style="display:none;">Go Back</button>
            <button type="button" id="getMetadata" style="margin-right:5%; display:none; width:auto;" class="button button-primary button-large modal-button">Download metadata XML</button>
            <button type="button" id="getCert" class="button button-primary button-large modal-button" style="display:none; width:auto;">Download certificate</button>
        </div>

    </div>
</div>

<style>

    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.6); /* Black w/ opacity */
        transition: all 1s;
    }

    /* Modal Content/Box */
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto; /* 15% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 45%; /* Could be more or less, depending on screen size */

        border-radius: 20px;
        box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
    }

    .modal-header {
        padding: 15px;
        border-bottom: 1px solid #e5e5e5;
    }

    .modal-title {
        text-align: center; 
        color: red;
    }

    .modal-body {
        position: relative;
        padding: 15px;
        text-align:center;
    }

    .modal-footer {
        padding: 15px;
        text-align: center;
        border-top: 1px solid #e5e5e5;
    }

    /* The Close Button */
    .modal-close-x {
        color: #aaa;
        float: right;
        font-size: 30px;
        font-weight: bold;
    }

    .modal-close-x:hover,
    .modal-close-x:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    } 

    .modal-button {
        width: 14%;
        height: 40px;
        font-size: 18px !important;
        white-space: normal;
        word-wrap: break-word;
    }

</style>

<script>';
if ($expired_cert==1){ 
echo '    window.onload = function() {
        document.getElementById("mo-idp-successDiv").style.display = "block";
        setTimeout(function(){
            document.getElementById("mo-idp-successDiv").style.display = "none";
        }, 105000);
    }';
    update_site_option("mo_idp_new_certs",'2');
}

echo '
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = document.getElementById("myBtn");

    var SPupdatedmsg = document.getElementById("isSPupdated");
    var confirmmsg = document.getElementById("confirmation");
    var downloadmsg = document.getElementById("downloadMsg");

    var yesBtn = document.getElementById("q1yes");
    var noBtn = document.getElementById("q1no");
    var updateBtn = document.getElementById("confirmUpdate");
    var goBackBtn = document.getElementById("goBack");
    var metadataBtn = document.getElementById("getMetadata");
    var certBtn = document.getElementById("getCert");

    // Get the <span> element that closes the modal
    var closex = document.getElementsByClassName("modal-close-x")[0];

    // When the user clicks on the button, open the modal
    if(btn){    
        btn.onclick = function() {
            modal.style.display = "block";
        }
    }

    // When the user clicks on <span> (x), close the modal
    if(closex){    
        closex.onclick = function() {
            yesBtn.style.display = "inline-block";
            noBtn.style.display = "inline-block";
            updateBtn.style.display = "none";
            goBackBtn.style.display = "none";
            metadataBtn.style.display = "none";
            certBtn.style.display = "none";
            SPupdatedmsg.style.display = "block";
            confirmmsg.style.display = "none";
            downloadmsg.style.display = "none";
            modal.style.display = "none";
        }
    }

    if(yesBtn){    
        yesBtn.onclick = function() {
            yesBtn.style.display = "none";
            noBtn.style.display = "none";
            updateBtn.style.display = "inline-block";
            goBackBtn.style.display = "inline-block";
            SPupdatedmsg.style.display = "none";
            confirmmsg.style.display = "block";
        }
    }

    if(noBtn){    
        noBtn.onclick = function() {
            yesBtn.style.display = "none";
            noBtn.style.display = "none";
            metadataBtn.style.display = "inline-block";
            certBtn.style.display = "inline-block";
            SPupdatedmsg.style.display = "none";
            downloadmsg.style.display = "block";
        }
    }
';
echo '
    if(metadataBtn){
        metadataBtn.onclick = function() {
            yesBtn.style.display = "inline-block";
            noBtn.style.display = "inline-block";
            metadataBtn.style.display = "none";
            certBtn.style.display = "none";
            SPupdatedmsg.style.display = "block";
            downloadmsg.style.display = "none";
            modal.style.display = "none";
            window.open("'.esc_url($metadata_url).'","_blank");
        }
    }';

    echo '

    if(certBtn){
        certBtn.onclick = function() {
            yesBtn.style.display = "inline-block";
            noBtn.style.display = "inline-block";
            metadataBtn.style.display = "none";
            certBtn.style.display = "none";
            SPupdatedmsg.style.display = "block";
            downloadmsg.style.display = "none";
            modal.style.display = "none";
            var ncurl = "' .esc_url(addslashes($new_certificate_url)).'";
            console.log(ncurl);
            
            window.location.href="'. esc_url(addslashes($new_certificate_url)).'";
        }
    }';

    echo'
    if(updateBtn){
        updateBtn.onclick = function() {
            document.getElementById(\'mo_idp_new_cert_form\').submit();
        }
    }

    if(goBackBtn){
        goBackBtn.onclick = function() {
            yesBtn.style.display = "inline-block";
            noBtn.style.display = "inline-block";
            updateBtn.style.display = "none";
            goBackBtn.style.display = "none";
            SPupdatedmsg.style.display = "block";
            confirmmsg.style.display = "none";
            modal.style.display = "none";
        }
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            yesBtn.style.display = "inline-block";
            noBtn.style.display = "inline-block";
            updateBtn.style.display = "none";
            goBackBtn.style.display = "none";
            metadataBtn.style.display = "none";
            certBtn.style.display = "none";
            SPupdatedmsg.style.display = "block";
            confirmmsg.style.display = "none";
            downloadmsg.style.display = "none";
            modal.style.display = "none";
        }
    } 

    function confirmUpdate()
    {
        modal.style.display = "none";
        var txt="Warning! Updating certificates will break your current SSO.\n Please update your Service Provider with the latest metadata XML/certificates to resume the SSO.";
        var r=confirm(txt);
        if(r == true)
        {
            document.getElementById(\'mo_idp_new_cert_form\').submit();
        }
    }

</script>';

