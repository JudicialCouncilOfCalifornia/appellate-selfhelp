<?php
global $MowafUtility,$mmp_dirName;
if( isset( $_GET[ 'tab' ] ) ) {
		$active_tab = sanitize_text_field($_GET[ 'tab' ]);
} else {
		$active_tab = 'default';
}
include_once $mmp_dirName . 'views'.DIRECTORY_SEPARATOR.'login_spam.php';
?>