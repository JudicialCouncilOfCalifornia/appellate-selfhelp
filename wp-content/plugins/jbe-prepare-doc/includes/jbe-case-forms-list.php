<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
if(!is_user_logged_in()) {
	wp_redirect( "/prepare-doc" );
	exit;
}
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
define( 'PREPARE_DOC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
global $wpdb; $case_id = ""; $style="";
$user_id = wp_get_current_user()->ID;
$owner = wp_get_current_user()->user_login;
//redirec url in aem forms /wordpress/prepare-doc/case/?action=aem-form-submit&
if(isset($_GET["case_id"]) && !empty($_GET["case_id"])){
	$case_id = $_GET["case_id"];		
}else{
	$case_id = $_COOKIE["case_id"];
}
if(isset($_POST['submit'])){	
	$cname = $_POST['caseName'];
	$utype = $_POST['userType'];
    $cid = $_POST['case_id'];
	//var_dump($cid);
    //if($cid > $case_id){
        $table_name = $wpdb->prefix . "prepare_doc_case";
        $wpdb->update(
                    $table_name,
                    array('case_name' => $cname, 'user_type' => $utype), //data
                    array('case_id' => $cid, 'user_id' => $user_id), //data
                    array('%s', '%s'), //data format
                    array('%s', '%s') //data format
        );
    //}   
}
$sql = "SELECT * FROM {$wpdb->prefix}prepare_doc_forms";
$result = $wpdb->get_results( $sql, 'ARRAY_A' );
$prepare_data =  get_option( 'prepare_doc_info' );
$form_server_url = $prepare_data["aem_forms_server_url"];
$case_form_created = false;
if(isset($_GET["action"]) && !empty($_GET["action"]) && $_GET["action"] == 'aem-form-submit'){
	$sql1 = "SELECT userdataID FROM metadata WHERE status = 'submitted' AND owner='".$owner."' ORDER BY `jcr:lastModified` DESC LIMIT 1";
	
	$result1 = $wpdb->get_results( $sql1 );

	$userdataID = $result1[0]->userdataID;

	$table_name = $wpdb->prefix . "prepare_doc_case_forms";
	$sql2 = "SELECT * FROM ".$table_name." WHERE userdataID = '".$userdataID."' AND user_id='".$user_id."' AND case_id='".$case_id."'";
	$result2 = $wpdb->get_results( $sql2 );

	if(count($result2) == 0) {
		$wpdb->insert(
			$table_name,
			array('case_id' => $case_id, 'user_id' => $user_id, 'userdataID' => $userdataID), //data
			array('%s', '%s', '%s') //data format			
		);
		$case_form_created = true;
	}
}
$casesql = "SELECT * FROM {$wpdb->prefix}prepare_doc_case WHERE case_id=".$case_id." AND user_id=".$user_id;
$caseresult = $wpdb->get_results( $casesql, 'ARRAY_A' );
?>
<div id='case-container'>
<h1 class=''><?php echo $caseresult[0]['case_name']; ?></h1>
<?php if($case_form_created){ ?>
<div class='case-submitted'>You have completed your <?php echo $_GET["form_name"];?> and will receive a PDF copy in your email. This document will need to be served to all parties before filed with the courts.</div>
<?php } ?>
<div class='prepare-doc-forms'>
<form id='formcase' method="POST" name="addCase">
    <div class='case-options'>
	<?php if(ICL_LANGUAGE_CODE == "en"){?>
	<a href="<?php echo get_site_url(); ?>/prepare-doc?action=delete&case_id=<?php echo $case_id; ?>" class='delete'><span class="en">Delete</span><span class="es">Eliminar</span></a>
    <div class='edit'><span class="en">Edit</span><span class="es">Editar</span></div>
	<a href="<?php echo get_site_url(); ?>/prepare-doc" class='case-button'><span class="en">See all cases</span><span class="es">Ver todos los casos</span></a>
	<?php }else{?>
	<a href="<?php echo get_site_url(); ?>/preparar-doc?action=delete&case_id=<?php echo $case_id; ?>" class='delete'><span class="en">Delete</span><span class="es">Eliminar</span></a>
    <div class='edit'><span class="en">Edit</span><span class="es">Editar</span></div>
	<a href="<?php echo get_site_url(); ?>/preparar-doc/" class='case-button'><span class="en">See all cases</span><span class="es">Ver todos los casos</span></a>
	<?php }?>
	</div>
    <div class='case-details-container'>
		<h2 class="en">Update a case</h2>
		<h2 class="es">Actualizar un caso</h2>
		<div class='case-table firstchild'><div class='case-cell'><span class='en'>Case Name</span><span class='es'>Nombre del caso</span></div></div>
		<div class='case-table fullwidth'><div class='case-cell'><input type="text" id="case-name" name='caseName' value="<?php echo $caseresult[0]['case_name']; ?>"></div></div>
		<p><span class='en'>Choose a name to call the case for your personal use. This will not show up an any documents or be submitted to the court.</span><span class='es'>Elija un nombre para llamar al caso para su uso personal. Esto no mostrará ningún documento ni se presentará a la corte.</span></p>
		<p><span class='en'>Para este caso...</span><span class='es'></span></p>
		<div class='case-table'><div class='case-cell'><input type="radio" value="1" <?php echo $caseresult[0]['user_type']==1 ? 'checked' : ''; ?> name="userType"></div><div class='case-cell'><span class='en'> I am starting an appeal, in the process of appealing a judgment; or an attorney representing an appellant.</span><span class='es'> Estoy comenzando una apelación, en el proceso de apelar una sentencia; o un abogado que representa a un apelante.</span></div></div>
		<div class='case-table'><div class='case-cell'><input type="radio" value="2" <?php echo $caseresult[0]['user_type']==2 ? 'checked' : ''; ?> name="userType"></div><div class='case-cell'><span class='en'> I am respondent in an appeal, or an attorney representing a respondent.</span><span class='es'>Soy demandado en una apelación, o un abogado que representa a un demandado.</span></div></div>
		<!--<div class='case-table'><div class='case-cell'><input type="radio" value="3" <?php echo $caseresult[0]['user_type']==3 ? 'checked' : ''; ?> name="userType"></div><div class='case-cell'>&nbsp;I have a disability that needs special accommodation.</div></div>
		<div class='case-table'><div class='case-cell'><input type="radio" value="4" <?php echo $caseresult[0]['user_type']==4 ? 'checked' : ''; ?> name="userType"></div><div class='case-cell'>&nbsp;I have been declared a vexatious litigant (or my client has.)</div></div>-->
        <input type="hidden" value="<?php echo $case_id; ?>" name="case_id">
		<div class='button-container'><div class='cancel'><span class='en'>Cancel</span><span class='es'>Cancelar</span></div><?php if(ICL_LANGUAGE_CODE == "en"){?><input type='submit' class='form-submit' name="submit" value="Update"><?php }else{ ?><input type='submit' class='form-submit' name="submit" value="Actualizar"><?php } ?></div>
	</div>
</form>
</div>
<?php
$sql = "SELECT * FROM metadata m INNER JOIN wp_prepare_doc_case_forms cf ON  m.userdataID = cf.userdataID WHERE m.owner='".$owner."' AND cf.case_id ='".$case_id."'"; 
$forms_list = $wpdb->get_results( $sql, 'ARRAY_A' );
$drafts =  $submitted = '';
$noofdrafts = $noofsubmitted = 0;
if(sizeof($forms_list) > 0) echo "<ul class='ul-table'><li><div><span class='en'>Form Name</span><span class='es'>Nombre del formulario</span></div><div></div></li>";
	
foreach($forms_list as $keys=>$values){	
    
	if($values['nodeType'] == 'fp:submittedForm' ){
		if(ICL_LANGUAGE_CODE == "en"){
		    echo '<li><div class=""><a tabindex="0" title="Show submitted form" class="__FP_submittedFormLink"  href="'.$uri.'case-form?submit_id='.$values["submitID"].'&form_name='.$values["formName"].'">'.$values["formName"].'</a></div><div><a tabindex="0" title="Start a new form using this form data" class="__FP_newInstance" href="'.$uri.'case-form?submit_id='.$values["submitID"].'&fpNewInstance=true&form_name='.$values["formName"].'">New</a><a tabindex="0" title="Get PDF" class="__FP_dor " href="'.$form_server_url.'/content/forms/portal/render.dor.pdf/'.$values["submitID"].'" target="_blank">PDF</a><a tabindex="0" title="Delete" class="__FP_deleteSubmission form-delete" submitid="'.$values["submitID"].'" id="'.$values["submitID"].'" userdataID="'.$values["userdataID"].'">Delete</a></div></li>';
		}else{
		    echo '<li><div class=""><a tabindex="0" title="Mostrar formulario enviado" class="__FP_submittedFormLink"  href="'.$uri.'forma-de-caso?lang=es&submit_id='.$values["submitID"].'&form_name='.$values["formName"].'">'.$values["formName"].'</a></div><div><a tabindex="0" title="Comience un nuevo formulario utilizando estos datos del formulario class="__FP_newInstance" href="'.$uri.'forma-de-caso?lang=es&submit_id='.$values["submitID"].'&fpNewInstance=true&form_name='.$values["formName"].'">Nuevo</a><a tabindex="0" title="Obtener PDF" class="__FP_dor " href="'.$form_server_url.'/content/forms/portal/render.dor.pdf/'.$values["submitID"].'" target="_blank">PDF</a><a tabindex="0" title="Eliminar" class="__FP_deleteSubmission form-delete" submitid="'.$values["submitID"].'" id="'.$values["submitID"].'" userdataID="'.$values["userdataID"].'">Delete</a></div></li>';
		}
		$noofsubmitted++;
	}else{
	    if(ICL_LANGUAGE_CODE == "en"){
		    echo'<li><div class=""><a tabindex="0" title="Show saved form" class="__FP_draftlink" href="'.$uri.'case-form?draft_id='.$values["draftID"].'&form_name='.$values["formName"].'">'.$values["formName"].'</a></div><div><a tabindex="0" title="Start a new form using this form data" class="__FP_newInstance"  href="'.$uri.'case-form?draft_id='.$values["draftID"].'&fpNewInstance=true&form_name='.$values["formName"].'">New</a><a tabindex="0" title="Delete" class="__FP_deleteDraft form-delete" draftid="'.$values["draftID"].'" formpath="'.$values["formPath"].'" formtype="'.$values["formType"].'" id="'.$values["draftID"].'" userdataID="'.$values["userdataID"].'">Delete</a></div></li>';
	    }else{
	        echo'<li><div class=""><a tabindex="0" title="Mostrar formulario guardado" class="__FP_draftlink" href="'.$uri.'forma-de-caso?lang=es&draft_id='.$values["draftID"].'&form_name='.$values["formName"].'">'.$values["formName"].'</a></div><div><a tabindex="0" title="Comience un nuevo formulario utilizando estos datos del formulario" class="__FP_newInstance"  href="'.$uri.'forma-de-caso?lang=es&draft_id='.$values["draftID"].'&fpNewInstance=true&form_name='.$values["formName"].'">Nuevo</a><a tabindex="0" title="Eliminar" class="__FP_deleteDraft form-delete" draftid="'.$values["draftID"].'" formpath="'.$values["formPath"].'" formtype="'.$values["formType"].'" id="'.$values["draftID"].'" userdataID="'.$values["userdataID"].'">Delete</a></div></li>'; 
	    }
		$noofdrafts++;
	}
}
if(sizeof($forms_list) > 0)echo "</ul>";
//http://localhost:4502/content/forms/portal/render.html/draft/6GUSAMWKDKLJY3VJMWWQYVPYAM_af?wcmmode=disabled
//<a tabindex="0" title="Get PDF" class="__FP_dor " href="/content/forms/portal/render.dor.pdf/NVZFQ6WN7BENV5NK2OWSP2UK4Q" target="_blank"></a>
//http://localhost:4502/content/forms/portal/render.html/draft/7LGCAOKTSSWBUHBPRRRSTFJ4KQ_af?fpNewInstance=true&wcmmode=disabled
//http://localhost:4502/content/forms/portal/render.html/submission/7YTOSWAD6O3E5WXWZCLZ7BMRQ4?fpNewInstance=true&wcmmode=disabled
?>
<!--<div class="tab">
  <button class="tablinks" onclick="userFormTabs(event, 'Draft')" id="defaultOpen">Draft Forms(<?php echo $noofdrafts; ?>)</button>
  <button class="tablinks" onclick="userFormTabs(event, 'Submitted')">Submitted Forms(<?php echo $noofsubmitted; ?>)</button>
</div>

<div id="Draft" class="tabcontent">
  <span onclick="this.parentElement.style.display='none'" class="topright">&times</span>
  <input type="text" id="draft-search" onkeyup="formSearch('draft-search','draftUl')" placeholder="Search for draft forms.." title="Type in a form name">
  <ul id='draftUl' class='ul-table'>
  <?php echo $drafts; ?>
  </ul>
</div>

<div id="Submitted" class="tabcontent">
  <span onclick="this.parentElement.style.display='none'" class="topright">&times</span>
  <input type="text" id="submit-search" onkeyup="formSearch('submit-search','submitUl')" placeholder="Search for submitted forms.." title="Type in a form name">
  <ul id='submitUl' class='ul-table'>
  <?php echo $submitted; ?> 
  </ul>
</div>
<input type="text" id="forms-search" onkeyup="formsSearch('forms-search','aem-forms')" placeholder="Search for forms.." title="Type in a form name">-->
<div id='aem-forms'>
<p><span class="en">Add a new form to the case</span><span class="es">Agregar un nuevo formulario al caso</span></p>
<select name="aem-form">
 <option value="javascript:void(0)">Select form</option>
<?php
$i = 0;
foreach($result as $keys=>$values){
	
	//echo '<option value="'.$uri.'case-form?form_id='.$values['form_id'].'&case_id='.$case_id.'&form_name='.stripslashes($values['form_name']).'">'.stripslashes($values['form_name']).'</option>';
	if(ICL_LANGUAGE_CODE == "en"){
		echo '<option value="'.$uri.'case-form?form_id='.$values['form_id'].'&case_id='.$case_id.'&form_name='.stripslashes($values['form_name']).'">'.stripslashes($values['form_name']).'</option>';
	}else{
		echo '<option value="'.$uri.'forma-de-caso/?lang=es&form_id='.$values['form_id'].'&case_id='.$case_id.'&form_name='.stripslashes($values['form_name']).'">'.stripslashes($values['form_name']).'</option>';
	}
	//echo '<option value="'.stripslashes($values['form_url']).'">'.stripslashes($values['form_name']).'</option>';
}
?>
</select><a href="javascript:void(0)" id="addForm"><span class="en">Add</span><span class="es">Añadir</span></a>
<div class="error form-error en">Please select form.</div>
<div class="error form-error es">Por favor seleccione el formulario.</div>
</div>
</div>

