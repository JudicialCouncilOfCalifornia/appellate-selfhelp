<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'PREPARE_DOC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
$uri = get_site_url();//$_SERVER['REQUEST_URI'];
$prepare_data =  get_option( 'prepare_doc_info' );
global $wpdb; $case_id = ""; $style="";
$user_id = wp_get_current_user()->ID;
$case_id = get_user_meta($user_id, "user_last_case_id", true);
if(isset($_POST['submit'])){	
	$cname = $_POST['caseName'];
	$utype = $_POST['userType'];
    $cid = $_POST['case_id'];
    if($cid > $case_id){
        $table_name = $wpdb->prefix . "prepare_doc_case";
        $wpdb->insert(
                    $table_name,
                    array('case_id' => $cid, 'case_name' => $cname, 'user_type' => $utype, 'user_id' => $user_id), //data
                    array('%s', '%s', '%s', '%s') //data format			
        );      
        update_user_meta($user_id, "user_last_case_id", $cid);
    }
}
if(isset($_GET["action"]) && !empty($_GET["action"]) && $_GET["action"] == 'delete' && !empty($_GET['case_id'])){    
    $table = 'metadata';
    $table1 = 'data';
    $table2 = 'additionalmetadatatable';
    $table3 = 'wp_prepare_doc_case_forms';
    $casesql = "SELECT userdataID FROM {$wpdb->prefix}prepare_doc_case_forms WHERE user_id=".$user_id." AND case_id=".$_GET['case_id'];
    $caseresult = $wpdb->get_results( $casesql, 'ARRAY_A' );
    foreach($caseresult as $key => $value){
        $userdataID = $value['userdataID'];    
        $metasql = "SELECT id FROM metadata WHERE userdataID=".$userdataID;
        $metaresult = $wpdb->get_results( $metasql, 'ARRAY_A' );
        foreach($metaresult as $k => $v){
            $id = $v['id'];            
            $datasql = "SELECT value FROM additionalmetadatatable a WHERE a.key ='dorID' AND a.id='".$id."'";
            $dataresult = $wpdb->get_results( $datasql, 'ARRAY_A' );          
            foreach($dataresult as $k1 => $v1){
                $wpdb->delete( $table1, array( 'id' => $v1['value'] ) );
            }
            $wpdb->delete( $table, array( 'id' => $id ) );
            $wpdb->delete( $table1, array( 'id' => $userdataID ) );
            $wpdb->delete( $table2, array( 'id' => $id ) );
            $wpdb->delete( $table3, array( 'userdataID' => $userdataID ) );
        }
    }
    $wpdb->delete( "wp_prepare_doc_case", array( 'case_id' => $_GET['case_id'], 'user_id' => $user_id) );
}
if($case_id == ""){ $case_id = 1;
?>
<div id="prepare-doc-login">
<div class='prepare-doc-intro'>
<h1 class='en'>Prepare Documents</h1>
<h1 class='es'>Preparar documentos</h1>
<div class='case-row intro'>
<div class='case-col'><div class="en"><?php echo $prepare_data["intro"] ?></div><div class="es">Nuestra aplicación lo guía a través de documentos para un caso de apelación, ya sea que esté apelando un fallo, respondiendo a una apelación o representando a un litigante. Cuando esté listo para archivar, puede archivar o imprimir los documentos para enviarlos por correo.</div></div></div>
<!--<div class='case-col'><a href='<?php echo $prepare_data["video_url"] != "" ? $prepare_data["video_url"] : "https://www.youtube.com/watch?v=RtKuyrR4NFY" ?>' class="magnific-popup video-img-icon mfp-iframe lightbox-added"><span class="overlay-icon"><span class="icon-overlay-inside"></span></span><img width="300" height="169" src="<?php echo $prepare_data["video_image"] != "" ? $prepare_data["video_image"] : '/wp-content/uploads/2019/02/kc-appeal-order-300x169.png'?>" class="attachment-medium size-medium" alt="" style="height: 100%; width: 100%;"><div class="carousel-slider__caption"><p class="caption" tabindex="0"><b>Vídeo:</b> <?php echo $prepare_data["video_title"] != "" ? $prepare_data["video_title"] : 'To Appeal or Not to Appeal, 6:29'?></p></div></a></div></div>-->

<?php if(!is_user_logged_in()) {?>
<div class='case-row user-login'>
<div>
<!--<h3 class=''>Already have an account?</h3>
<div class='case-col'><a href="#">YES</a><div>Great, sign in to your account to continue working.</div></div>
<div class='case-col'><a href="#">NO</a><div>Let's get you started with a new account, so your documents can be saved.</div></div>-->
<div class='case-col'><a href="/login"><span class="en">Sign in / Create an account</span><span class="es">Iniciar sesión / Crear una cuenta</span></a></div>
</div>
</div>
</div>
<?php
}else{
	echo "<div class='button-container'><div class='showAddcase'><span class='en'>Next</span><span class='es'>Próximo</span></div></div>";
}
?>
</div>
</div>
<?php
}else{
    $case_id = $case_id + 1;
	$style = "display:block";
}
?>
<div id='document-section'>
<div class='prepare-doc-case' style="<?php echo $style ?>">
<h1 class='en'>Prepare Documents</h1>
<h1 class='es'>Preparar documentos</h1>
<?php
    $sql = "SELECT * FROM {$wpdb->prefix}prepare_doc_case WHERE user_id=".$user_id." ORDER BY case_id ASC";
	$result = $wpdb->get_results( $sql, 'ARRAY_A' );
	$i = 0;
	if(sizeof($result) > 0) echo "<div class='case-container'><div class='row header'><div class='col'><span class='en'>Case Name</span><span class='es'>Nombre del caso</span></div></div>"; 
	foreach($result as $keys=>$values){	
	    if(ICL_LANGUAGE_CODE == "en"){
		    echo "<div class='row'><div class='col'><a href='".$uri."/prepare-doc/case?case_id=".$values['case_id']."'>".$values['case_name']."</a></div></div>";
	    }else{
	       echo "<div class='row'><div class='col'><a href='".$uri."/preparar-doc/caso/?lang=es&case_id=".$values['case_id']."'>".$values['case_name']."</a></div></div>"; 
	    }
	}
	if(sizeof($result) > 0) echo "</div>";
	echo "<div class='add-case cases'><a href='#'><span class='en'>Add a case</span><span class='es'>Agregar un caso</span></a></div>";
?>
</div>
<div class='prepare-doc-case-details'>
<h1 class='en'>Add a case</h1>
<h1 class='es'>Agregar un caso</h1>
	<form id='formcase' method="POST" name="addCase">
	<div class='case-details-container'>
		<!--<h3>Add a case</h3>-->
		<div class='case-table'><div class='case-cell'><span class='en'>Case Name</span><span class='es'>Nombre del caso</span></div></div>
		<div class='case-table'><div class='case-cell'><input type="text" id="case-name" name='caseName'></div></div>
		<p><span class='en'>Choose a name to call the case for your personal use. This will not show up an any documents or be submitted to the court.</span><span class='es'>Elija un nombre para llamar al caso para su uso personal. Esto no mostrará ningún documento ni se presentará a la corte.<span></span></p>
		<p><span class='en'>Para este caso...</span><span class='es'></span></p>
		<div class='case-table'><div class='case-cell'><input type="radio" value="1" name="userType"></div><div class='case-cell'><span class='en'> I am starting an appeal, in the process of appealing a judgment; or an attorney representing an appellant.</span><span class='es'> Estoy comenzando una apelación, en el proceso de apelar una sentencia; o un abogado que representa a un apelante.</span></div></div>
		<div class='case-table'><div class='case-cell'><input type="radio" value="2" name="userType"></div><div class='case-cell'><span class='en'> I am respondent in an appeal, or an attorney representing a respondent.</span><span class='es'>Soy demandado en una apelación, o un abogado que representa a un demandado.</span></div></div>
		<!--<div class='case-table'><div class='case-cell'><input type="radio" value="3" name="userType"></div><div class='case-cell'>&nbsp;I have a disability that needs special accommodation.</div></div>
		<div class='case-table'><div class='case-cell'><input type="radio" value="4" name="userType"></div><div class='case-cell'>&nbsp;I have been declared a vexatious litigant (or my client has.)</div></div>-->
        <input type="hidden" value="<?php echo $case_id; ?>" name="case_id">
		<div class='button-container'><div class='showAddcase'><span class='en'>Cancel</span><span class='es'>Cancelar</span></div><?php if(ICL_LANGUAGE_CODE == "en"){?><input type='submit' class='form-submit' name="submit" value="Next"><?php }else{ ?><input type='submit' class='form-submit' name="submit" value="Próximo"><?php } ?></div>
	</div>
	</form>
</div>
</div>
<div id="div1" style='display:none'><iframe src='https://selfhelp.appellate.courts.ca.gov/?option=testConfig&amp;acs=https://judca-stage1.adobemsbasic.com/saml_login&amp;issuer=https://judca-stage1.adobemsbasic.com/id&amp;defaultRelayState='></iframe></div>