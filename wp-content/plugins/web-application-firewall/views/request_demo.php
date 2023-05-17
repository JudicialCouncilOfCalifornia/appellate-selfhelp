<div class="mo_waf_divided_layout">
	<div class="mo_waf_setting_layout">
		<h3> Demo Request Form : </h3>
		<form method="post">
			<input type="hidden" name="option" value="mowaf_demo_request_form" />
			<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('mo2f-Request-demo')?>">
			<table cellpadding="4" cellspacing="4">
                        <tr>
						  	<td><strong>Usecase : </strong></td>
							<td>
							<textarea type="text" minlength="15" name="mo_wafA_demo_usecase" style="resize: vertical; width:350px; height:100px;" rows="4" placeholder="Write us about your usecase" required value=""></textarea>
							</td>
						  </tr> 	
                        <tr>
							<td></td>
							
						</tr>
			    		<tr>
							<td><strong>Email ID : </strong></td>
							<td><input required type="email" name="mo_wafA_demo_email" placeholder="Email id" value="" /></td>
						</tr>
						<tr>
							<td><strong>Request a demo for : </strong></td>
							<td>
								<select required  name="mo_wafA_demo_plan" id="mo_wafA_demo_plan_id">
									<option disabled selected>------------------ Select ------------------</option>
									<option value="WAF">Web Application Firewall (WAF) </option>
									<option value="login_spam">Login Security</option>
									<option value="notSure">Not Sure/Multiple options</option>
								</select>
							</td>
					  	</tr>
                        
			    	</table>
			    	<div style="padding-top: 10px;">
			    		<input type="submit" name="submit" value="Submit Demo Request" class="mo_waf_button mo_waf_button1" />
			    	</div>	
		</form>		
	</div>
</div>