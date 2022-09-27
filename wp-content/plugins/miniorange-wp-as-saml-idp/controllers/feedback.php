<?php

$handler    = \IDP\Handler\FeedbackHandler::instance();
$message    = 'We are sad to see you go :( 
                    Have you found a bug? Did you feel something was missing? 
                    Whatever it is, we\'d love to hear from you and get better.';
$nonce      = $handler->_nonce;
$keepSettingsIntact = get_site_option('idp_keep_settings_intact');

include MSI_DIR . 'views/feedback.php';