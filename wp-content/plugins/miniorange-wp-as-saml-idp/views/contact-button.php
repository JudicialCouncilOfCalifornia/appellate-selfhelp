<?php
echo'
	<div onclick="toggleContactForm()">
		<div class="mo-idp-contact-container-div">';
			echo'<div class="mo-idp-contact-text-div" style="background-color: '.esc_attr($colors[2]).' !important;">
				<span class="mo-idp-contact-arrow" style="background-color: '.esc_attr($colors[2]).';"></span>
				<div class="mo-idp-contact-text">
					<b>Hello there!<br>Need Help? Contact Us!</b>
				</div>
			</div>';
			echo'<div class="mo-idp-contact-button-div" style="background: '.esc_attr($colors[2]).';">
				<div class="mo-idp-contact-button"><span class="dashicons dashicons-email-alt" style="color:white; font-size:3.125rem !important;padding:0.4rem;"></span></div>
			</div>';
		echo'</div>
	</div>
	<div class="mo-idp-contact-form" id="idp-contact-button-form" hidden>
		<h3>CONTACT US</h3>';
		echo'<hr class="mo-idp-contact-hr" style="background-color: '.esc_attr($colors[2]).';">';
		echo'<p style="font-size:1rem;">Need any help? Send us a query and we will get in touch.</p>
		<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_idp_contact_us_query_option"/>
			<table style="width:100%;margin-bottom:2%;">
				<tr>
					<td>';
						echo'<input 	type="email" 
								class="mo-idp-contact-input" required 
								name="mo_idp_contact_us_email" 
								value="'.esc_attr($email).'" 
								placeholder="Enter your email">';
					echo'</td>
                </tr>
                <tr>
					<td>';
                		echo'<input 	type="tel" 
								id="contact_us_phone" 
								pattern="[\+]?[0-9]{1,4}[\s]?([0-9]{4,12})*" 
								class="mo-idp-contact-input" 
                        		name="mo_idp_contact_us_phone" 
								value="'.esc_attr($phone).'" 
								placeholder="Enter your phone number with country code (+1)">';
					echo'</td>
                </tr>
                <tr>
					<td>
                		<textarea 	style="width:98%;font-size:1rem;resize:vertical;" 
									name="mo_idp_contact_us_query" required
									rows="4" 
									placeholder="Write your query here"></textarea>
					</td>
                </tr>
            </table>
            <div style="text-align: center;">';
            	echo'<input type="submit" name="submit" value="Submit Query" class="mo-idp-contact-submit" style="background: '.esc_attr($colors[2]).'; border-color: '.esc_attr($colors[2]).'; cursor:pointer;">';
			echo'</div>
        </form>';
        echo'<p style="font-size: 0.875rem;"><b>Reach out to us at <span>'.esc_attr($support).'</span></b></p>';
   echo' </div>';