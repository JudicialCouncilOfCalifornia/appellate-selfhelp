<?php 

if ( ! defined( 'ABSPATH' ) ) exit;
if(!is_user_logged_in()) {
	wp_redirect( "/prepare-doc" );
	exit;
}
define( 'PREPARE_DOC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
global $wpdb; $case_id = ""; $style="";
$user_id = wp_get_current_user()->ID;
$owner = wp_get_current_user()->user_login;
$prepare_data =  get_option( 'prepare_doc_info' );
$form_server_url = $prepare_data["aem_forms_server_url"];
?>
<br><script type='text/javascript'>
function GetQueryStringParams(sParam){
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++){
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam){
            return sParameterName[1];
		}
	}
};
function setCookie(cname,cvalue,exdays) {
  var d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  var expires = "expires=" + d.toGMTString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
var case_id = GetQueryStringParams('case_id');
if(case_id != "" && case_id != undefined){
    setCookie("case_id", case_id, 1);
}    
</script>
<?php
if(isset($_GET["case_id"]) && !empty($_GET["case_id"])){
	$case_id = $_GET["case_id"];		
}else{
	$case_id = $_COOKIE["case_id"];
}
if(isset($_GET["form_id"]) && !empty($_GET["form_id"])){
	$form_id = $_GET["form_id"];
?>
<script>
    var options = {path:"<?php echo $prepare_data['aem_forms_directory'].'/'.$form_id ?>.html", dataRef:"", themepath:"", CSS_Selector:".aem-form"};//http://localhost/wordpress/wp-content/uploads/jbe_global_xmls/prefill-sample1.xml
    var data = { "dataRef": options.dataRef,"afAcceptLang":<?php if(ICL_LANGUAGE_CODE == "es") echo '"es"'; else echo '"en"';?> };//wcmmode : "disabled", 
</script>
<?php
}
if(isset($_GET["draft_id"]) && !empty($_GET["draft_id"])){
	$draft_id = $_GET["draft_id"];
?>
<script>
    var options = {path:"<?php echo $form_server_url.'/content/forms/portal/render.html/draft/'.$draft_id ?>", dataRef:"", themepath:"", CSS_Selector:".aem-form"};
<?php
if(isset($_GET["fpNewInstance"]) && !empty($_GET["fpNewInstance"])){
	$fpNewInstance = $_GET["fpNewInstance"];
?>   
    var data = { wcmmode : "disabled", fpNewInstance: <?php echo $fpNewInstance ?>, "afAcceptLang":<?php if(ICL_LANGUAGE_CODE == "es") echo '"es"'; else echo '"en"';?>}; 
<?php }else{ ?>
    var data = { wcmmode : "disabled", "afAcceptLang":<?php if(ICL_LANGUAGE_CODE == "es") echo '"es"'; else echo '"en"';?>};
<?php } ?>
    </script>
<?php
}
if(isset($_GET["submit_id"]) && !empty($_GET["submit_id"])){
	$submit_id = $_GET["submit_id"];
?>
<script>
    var options = {path:"<?php echo $form_server_url.'/content/forms/portal/render.html/submission/'.$submit_id ?>", dataRef:"", themepath:"", CSS_Selector:".aem-form"};
    <?php
if(isset($_GET["fpNewInstance"]) && !empty($_GET["fpNewInstance"])){
	$fpNewInstance = $_GET["fpNewInstance"];
?>   
    var data = { wcmmode : "disabled", fpNewInstance: <?php echo $fpNewInstance ?>, "afAcceptLang":<?php if(ICL_LANGUAGE_CODE == "es") echo '"es"'; else echo '"en"';?>}; 
<?php }else{ ?>
    var data = { wcmmode : "disabled", "afAcceptLang":<?php if(ICL_LANGUAGE_CODE == "es") echo '"es"'; else echo '"en"';?>};
<?php } ?>
    </script>
<?php
}
?>
<h1 class=''><?php echo stripslashes($_GET["form_name"]); ?></h1>
<a href="<?php echo get_site_url(); ?>/prepare-doc" class='case-button'>See all cases</a>
<div class="aem-form">
    <p>Please wait for form loading...</p> 
</div>	
<script>
   /* var options = {path:"<?php echo $result[0]['form_url'] ?>", dataRef:"http://localhost/wordpress/wp-content/uploads/jbe_global_xmls/prefill-sample1.xml", themepath:"", CSS_Selector:".aem-form"};*/
    jQuery(document).ready(function() {
    var loadAdaptiveForm = function(options){
   
    if(options.path) {
		// alert(options.path);
        // options.path refers to the publish URL of the adaptive form
        // For Example: http:myserver:4503/content/forms/af/ABC, where ABC is the adaptive form
        // Note: If AEM server is running on a context path, the adaptive form URL must contain the context path 
        /*data = {
                // Set the wcmmode to be disabled
                wcmmode : "disabled",
                // Set the data reference, if any
				"dataRef": options.dataRef
                // Specify a different theme for the form object
              //  "themeOverride" : options.themepath
            }*/
        var path = options.path;
        path += "";
		//console.log(options);
        //console.log(data);
        jQuery.ajax({
            url  : path ,
			crossdomain: true,
            type : "GET",
            data : data,
            async: false,
            success: function (form) {			
				
				//form = form.replace(/\/content\//gi,"https://judca-stage1.adobemsbasic.com/content/");
				//form = form.replace(/\/libs\//gi,"https://judca-stage1.adobemsbasic.com/libs/");
				//form = form.replace(/\/etc\//gi,"https://judca-stage1.adobemsbasic.com/etc/");
				//form = form.replace(/\/etc.clientlibs\//gi,"https://judca-stage1.adobemsbasic.com/etc.clientlibs/");
				//console.log(form);
                // If jquery is loaded, set the inner html of the container
                // If jquery is not loaded, use APIs provided by document to set the inner HTML but these APIs would not evaluate the script tag in HTML as per the HTML5 spec
                // For example: document.getElementById().innerHTML
                if(window.jQuery && options.CSS_Selector){
					//console.log(form);
                    // HTML API of jquery extracts the tags, updates the DOM, and evaluates the code embedded in the script tag.
                    jQuery(options.CSS_Selector).html(form);
                }
            },
            error: function (form) {
                // any error handler
            }
        });
    } else {
        if (typeof(console) !== "undefined") {
            console.log("Path of Adaptive Form not specified to loadAdaptiveForm");
        }
    }
    }(options);
    });
    
</script>


<!--<div class="customafsection1"/>
    <p>This section is replaced with the adaptive form.</p>      
<script>
    var options = {path:"http://localhost:81/content/forms/portal/render.html/draft/7LGCAOKTSSWBUHBPRRRSTFJ4KQ_af", dataRef:"", themepath:"", CSS_Selector:".customafsection1"};
    var loadAdaptiveForm = function(options){
   
    if(options.path) {
		// alert(options.path);
        // options.path refers to the publish URL of the adaptive form
        // For Example: http:myserver:4503/content/forms/af/ABC, where ABC is the adaptive form
        // Note: If AEM server is running on a context path, the adaptive form URL must contain the context path 
        var path = options.path;
        path += "";
		console.log(options);
        jQuery.ajax({
            url  : path ,
            type : "GET",
            data : {
                // Set the wcmmode to be disabled
                wcmmode : "disabled",
                // Set the data reference, if any
				//"dataRef": options.dataRef
                // Specify a different theme for the form object
              //  "themeOverride" : options.themepath
            },
            async: false,
            success: function (data) {
                // If jquery is loaded, set the inner html of the container
                // If jquery is not loaded, use APIs provided by document to set the inner HTML but these APIs would not evaluate the script tag in HTML as per the HTML5 spec
                // For example: document.getElementById().innerHTML
                if(window.jQuery && options.CSS_Selector){
                    // HTML API of jquery extracts the tags, updates the DOM, and evaluates the code embedded in the script tag.
                    jQuery(options.CSS_Selector).html(data);
                }
            },
            error: function (data) {
                // any error handler
            }
        });
    } else {
        if (typeof(console) !== "undefined") {
            console.log("Path of Adaptive Form not specified to loadAdaptiveForm");
        }
    }
    }(options);
     
</script>-->
