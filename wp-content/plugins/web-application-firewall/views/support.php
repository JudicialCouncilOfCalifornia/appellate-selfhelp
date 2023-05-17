<?php
	

echo'	
		<div class="mo_wpns_support_form_popup" id="mo_wpns_support_form_popup" hidden>

		<div class="mo_wpns_support_layout">
			
			<img src="'.dirname(plugin_dir_url(__FILE__)).'/includes/images/support3.png">
			<h1>Support</h1>
			<p>Need any help? We are available any time, Just send us a query so we can help you.</p>
				<form name="f" method="post" action="">
					<input type="hidden" name="option" value="mo_wpns_send_query"/>
					<table class="mo_wpns_settings_table">
						<tr><td>
							<input type="email" class="mo_wpns_table_textbox" id="query_email" name="query_email" value="'.esc_attr($email).'" placeholder="Enter your email" required />
							</td>
						</tr>
						<tr><td>
							<input type="text" class="mo_wpns_table_textbox" name="query_phone" id="query_phone" value="'.esc_attr($phone).'" placeholder="Enter your phone"/>
							</td>
						</tr>
						<tr>
							<td>
								<textarea id="query" name="query" class="mo_wpns_settings_textarea" style="resize: vertical;width:100%" cols="52" rows="7" onkeyup="mo_wpns_valid(this)" onblur="mo_wpns_valid(this)" onkeypress="mo_wpns_valid(this)" placeholder="Write your query here"></textarea>
							</td>
						</tr>
					</table>
					<input type="submit" name="send_query" id="send_query" value="Submit Query" style="margin-bottom:3%;" class="button button-primary button-large" />
				</form>
				<br />			
		</div>
		</div>
		<div class="mo_wpns_support_button" id="mo_wpns_support_button">
			<span class="dashicons dashicons-email-alt"></span>
		</div>
		<script>
		jQuery("#mo_wpns_support_button").click(()=>{
			jQuery("#mo_wpns_support_form_popup").slideToggle();
		})
			
		</script>';
?>
