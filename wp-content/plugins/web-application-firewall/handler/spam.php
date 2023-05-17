<?php
	
	class Mowaf_Spam
	{
		function __construct()
		{
			if(get_option('mo_wpns_enable_comment_spam_blocking') || get_option('mo_wpns_enable_comment_recaptcha'))
			{
				add_filter( 'preprocess_comment'		, array($this, 'comment_spam_check'			) );
				add_action( 'comment_form_after_fields' , array($this, 'comment_spam_custom_field'	) );
				
			}
		}
		
		function comment_spam_check( $comment_data ) 
		{
			if(!is_user_logged_in()){
			global $MowafUtility;
			if( isset($_POST['mocomment']) && !empty($_POST['mocomment']))
				wp_die( __( 'You are not authorised to perform this action.'));
			else if(get_option('mo_wpns_enable_comment_recaptcha'))
			{
				if(is_wp_error($MowafUtility->verify_recaptcha($_POST['g-recaptcha-response'])))
					wp_die( __( 'Invalid captcha. Please verify captcha again.'));
			}
			return $comment_data;
		}
		else{
			return $comment_data;	
		}
		}

		function comment_spam_custom_field()
		{
			echo '<input type="hidden" name="mocomment" />';
			if(get_option('mo_wpns_enable_comment_recaptcha'))
			{
				echo '<script src="'.MowafConstants::RECAPTCHA_URL.'"></script>';
				echo '<div class="g-recaptcha" data-sitekey="'.get_option('mo_wpns_recaptcha_site_key').'"></div>';
			}
		}
	}
	new Mowaf_Spam;