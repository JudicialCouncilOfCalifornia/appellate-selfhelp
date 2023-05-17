<?php

echo'<div>	
		<div class="mo_wpns_setting_layout">';

echo'		<h3>Block Registerations from fake users</h3>
			<div class="mo_wpns_subheading">
				Disallow Disposable / Fake / Temporary email addresses
			</div>
			
			<form id="mo_wpns_enable_fake_domain_blocking" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_enable_fake_domain_blocking">
				<input type="checkbox" name="mo_wpns_enable_fake_domain_blocking" '.esc_attr($domain_blocking).' onchange="document.getElementById(\'mo_wpns_enable_fake_domain_blocking\').submit();"> Enable blocking registrations from fake users.
			</form>
		</div>
		
		<div class="mo_wpns_setting_layout">	
			<h3>Advanced User Verification</h3>
			<div class="mo_wpns_subheading">Verify identity of user by sending One Time Password ( OTP ) on his phone number or email address.</div>
			
			<form id="mo_wpns_advanced_user_verification" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_advanced_user_verification">
				<input type="checkbox" name="mo_wpns_enable_advanced_user_verification" '.esc_attr($user_verify).' onchange="document.getElementById(\'mo_wpns_advanced_user_verification\').submit();"> Enable advanced user verification<br>
			</form>';

			if($user_verify)
				echo esc_attr($html1);
			
echo'		
		</div>
		
		<div class="mo_wpns_setting_layout">	
			<h3>Social Login Integration</h3>
			<div class="mo_wpns_subheading">Allow your user to login and auto-register with their favourite social network like Google, Twitter, Facebook, Vkontakte, LinkedIn, Instagram, Amazon, Salesforce, Windows Live.</div>
			
			<form id="mo_wpns_social_integration" method="post" action="">
				<input type="hidden" name="option" value="mo_wpns_social_integration">
				<input type="checkbox" name="mo_wpns_enable_social_integration" '.esc_attr($social_login).' onchange="document.getElementById(\'mo_wpns_social_integration\').submit();"> Enable login and registrations with social networks.<br>
			    
			</form>';
			
			if($social_login)
				echo esc_attr($html2);
				
echo'	</div>
	</div>';