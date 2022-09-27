<?php

if(!function_exists('wpfront_safe_redirect')) {
    
    function wpfront_safe_redirect($location) {
        wp_safe_redirect($location);
        exit();
    }
    
}
