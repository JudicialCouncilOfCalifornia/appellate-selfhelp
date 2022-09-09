<?php
$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';
global $wpdb;
if($_POST['action']=="draft"){
    $user_id = wp_get_current_user()->ID;
    $owner = wp_get_current_user()->user_login;
    if(isset($_POST["case_id"]) && !empty($_POST["case_id"])){
        $case_id = $_POST["case_id"];		
    }else{
        $case_id = $_COOKIE["case_id"];
    }
    $sql1 = "SELECT userdataID FROM metadata WHERE nodeType = 'fp:Draft' and owner='".$owner."' ORDER BY 'jcr:lastModified' DESC LIMIT 1";
	$result1 = $wpdb->get_results( $sql1 );
	$userdataID = $result1[0]->userdataID;			
	$table_name = $wpdb->prefix . "prepare_doc_case_forms";
	$wpdb->insert(
        $table_name,
        array('case_id' => $case_id, 'user_id' => $user_id, 'userdataID' => $userdataID), //data
        array('%s', '%s', '%s') //data format			
    );
    echo "YES";
}else if($_POST['action']=="delete"){
    $table = 'metadata';
    $table1 = 'data';
    $table2 = 'additionalmetadatatable';
    $table3 = 'wp_prepare_doc_case_forms';
    $id = $_POST['id'];
    $userdataID = $_POST['userdataID'];
    $wpdb->delete( $table, array( 'id' => $id ) );
    $wpdb->delete( $table1, array( 'id' => $userdataID ) );
    $wpdb->delete( $table2, array( 'id' => $id ) );
    $wpdb->delete( $table3, array( 'userdataID' => $userdataID ) );
    echo "YES";
}else{
    echo "NO";
}
?>