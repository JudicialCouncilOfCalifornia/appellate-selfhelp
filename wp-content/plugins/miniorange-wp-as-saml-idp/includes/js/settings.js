jQuery(document).ready(function () {
    $idp = jQuery;

    // when the service provider dropdown value changes then
    // submit the change service provider form
    // so that the current service provider can be set
    $idp("select[name='service_provider']").change(function(){
    	 $idp("input[name='service_provider']").val($idp(this).val());
    	 $idp("#change_sp").submit();
    });

    // clicking any element with mo_idp_help_title class will trigger a
    // slidetoggle animation on nearest element having the mo-idp-help-desc
    // class
    $idp(".mo_idp_help_title").click(function(e){
    	e.preventDefault();
    	$idp(this).next('.mo-idp-help-desc').slideToggle(400);
    });

    $idp(".mo_idp_checkbox").click(function(){
        $idp(this).next('.mo-idp-help-desc').slideToggle(400);
    });

    $idp("#lk_check1").change(function(){
        if($idp("#lk_check2").is(":checked") && $idp("#lk_check1").is(":checked")){
            $idp("#activate_plugin").removeAttr('disabled');
        }
    });

    $idp("#lk_check2").change(function(){
        if($idp("#lk_check2").is(":checked") && $idp("#lk_check1").is(":checked")){
            $idp("#activate_plugin").removeAttr('disabled');
        }
    });

    // this is the ribbon styletopbar to choose between protocols
    $idp("div[class^='protocol_choice_'").click(function(){
        if(!$idp(this).hasClass("selected")){
            $idp(this).parent().parent().next("form").fadeOut();
            $idp("#add_sp input[name=\"action\"]").val($idp(this).data('toggle'));
            $idp(".mo-idp-loader").fadeIn();
            $idp("#add_sp").submit();
        }
    });

    // any element with copyClip class will
    // copy the text in the element having
    // copyBody class
    $idp(".mo-idp-copyClip").click(function(){
        $idp(this).next(".copyBody").select();
        document.execCommand('copy');
    });

    $idp('a[aria-label="Deactivate Login using WordPress Users"]').click(function(e){
        $idp("#mo-idp-feedback-modal").show();
        e.preventDefault();
    });

    // user is trying to remove his account
    $idp('#remove_accnt').click(function(e){
        $idp("#remove_accnt_form").submit();
    });

    // adminis trying to goback to the login page
    $idp("#goToLoginPage").click(function (e) {
        $idp("#goToLoginPageForm").submit();
    });

});

function showTestWindow(url) {
    var myWindow = window.open(url, "TEST SAML IDP", "scrollbars=1 width=800, height=600");
}

function deleteSpSettings() {
    jQuery("#mo_idp_delete_sp_settings_form").submit();
}

function mo_valid_query(f) {
    !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
            /[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
}

function mo2f_upgradeform(planType){
    jQuery("#requestOrigin").val(planType);
    jQuery("#mocf_loginform").submit();
}

//------------------------------------------------------
// FEEDBACK FORM FUNCTIONS
//------------------------------------------------------

//feedback forms stuff
function mo_idp_feedback_goback() {
    $idp("#mo-idp-feedback-modal").hide();
}

function copyToClipboard(copyButton, element, copyelement) {
    var temp = jQuery("<input>");
    jQuery("body").append(temp);
    temp.val(jQuery(element).text()).select();
    document.execCommand("copy");
    temp.remove();
    jQuery(copyelement).text("Copied");

    jQuery(copyButton).mouseout(function(){
        jQuery(copyelement).text("Copy to Clipboard");
    });
}

function gatherplaninfo(name,users){
    document.getElementById("plan-name").value=name;
    document.getElementById("plan-users").value=users;
    document.getElementById("mo_idp_request_quote_form").submit();
}

function toggleContactForm() {
    var contact_text = jQuery(".mo-idp-contact-container");
    var contact_form = jQuery("#idp-contact-button-form");
    if(contact_text.is(":hidden")){
        contact_text.show();
        contact_form.slideToggle();
    } else {
        contact_text.hide();
        contact_form.slideToggle();
    }
}

function mo_idp_dashboard_start()
{
    document.getElementsByClassName("mo-idp-text-center")[0].style.display = "block";
}

function mo_idp_dashboard_rest()
{
    document.getElementsByClassName("mo-idp-text-center")[0].style.display = "none";
}

/******Manual and Auto Toggle */

function moidpmanualhandler(){
    document.getElementById("mo-idp--manual_upload").style.display = "block";
    document.getElementById("mo-idp--auto-upload").style.display = "none";
    document.getElementById("mo-idp-manual-upload").classList.add("mo-idp-current-tab");
    document.getElementById("mo-idp-auto-upload").classList.remove("mo-idp-current-tab");
    document.getElementsByClassName("mo-idp-enter-data")[0].classList.add("mo-idp-text-color");
    document.getElementsByClassName("mo-idp-enter-data")[1].classList.remove("mo-idp-text-color");
    }

function moidpautohandler(){
    document.getElementById("mo-idp--manual_upload").style.display = "none";
    document.getElementById("mo-idp--auto-upload").style.display = "block";
    document.getElementById("mo-idp-manual-upload").classList.remove("mo-idp-current-tab");
	document.getElementById("mo-idp-auto-upload").classList.add("mo-idp-current-tab");
    document.getElementsByClassName("mo-idp-enter-data")[1].classList.add("mo-idp-text-color");
    document.getElementsByClassName("mo-idp-enter-data")[0].classList.remove("mo-idp-text-color");
}


		