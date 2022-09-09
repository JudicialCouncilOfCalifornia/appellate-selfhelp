<?php

$handler = \IDP\Handler\FeedbackHandler::instance();
$message = 'We are sad to see you go :( 
                Have you found a bug? Did you feel something was missing? 
                Whatever it is, we\'d love to hear from you and get better.';
$nonce   = $handler->_nonce;

include MSI_DIR . 'views/feedback.php';