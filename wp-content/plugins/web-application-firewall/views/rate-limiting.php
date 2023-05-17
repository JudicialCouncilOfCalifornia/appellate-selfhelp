<div class="mo_wpns_divided_layout">
     <div class="mo_wpns_setting_layout">
		<div id="RL" name="RL">
	    	<table style="width:100%">
			<tr>
			<th align="left">
			<h3>Rate Limiting:
				<br>
				<p><i class="mo_wpns_not_bold">This will protect your Website from Dos attack and block request after a limit exceed.</i></p>
	  		</th>
	  		<th align="right">
		  		<label class='mo_wpns_switch'>
				 <input type=checkbox id='rateL' name='rateL' />
				 <span class='mo_wpns_slider mo_wpns_round'></span>
				</label>
			</th>
		
			</h3>
			</tr>
			</table>
		</div>
		
		<div name = 'rateLFD' id ='rateLFD'>
		<table style="width: 100%"> 
		</h3>
		<tr><th align="left">
		<h3>Block user after</th>
		<th align="center"><input type="number" name="req" id="req" required min="1"/>
			<i class="mo_wpns_not_bold">Requests/min</i></h3>
		</th>

		<th>
		<h3>action
		<select id="action">
		  <option value="ThrottleIP">Throttle IP</option>
		  <option value="BlockIP">Block IP</option>
		</select>
		</h3>
		</th></tr>
		<tr><th></th>
		<th align="center">
			<br><input type="button" name="saveRateL" id="saveRateL" value="save" class="button button-primary button-large">
			</th>
		</tr>
		</table>
		</form>
		
		</div>
	</div>
	
	</div>
<script type="text/javascript">
	
	document.getElementById('rateLFD').style.display="none";	
	var Rate_request 	= "<?php echo esc_attr(get_option('Rate_request'));?>";
	var Rate_limiting 	= "<?php echo esc_attr(get_option('Rate_limiting'));?>";
	var actionValue		= "<?php echo esc_attr(get_option('actionRateL'));?>";

	if(Rate_limiting == '1')
	{

		jQuery('#rateL').prop("checked",true);
		jQuery('#req').val(Rate_request);
		if(actionValue == 0)
		{
			jQuery('#action').val('ThrottleIP');
		}
		else
		{
			jQuery('#action').val('BlockIP');
		}
		document.getElementById('rateLFD').style.display="block";
			
	}
	jQuery('#rateL').click(function(){
		var rateL 	= 	jQuery("input[name='rateL']:checked").val();
		if(rateL == 'on')
			document.getElementById('rateLFD').style.display="block";
		else
			document.getElementById('rateLFD').style.display="none";
			
		var Rate_request 	= "<?php echo esc_attr(get_option('Rate_request'));?>";
		var nonce = '<?php echo esc_attr(wp_create_nonce("RateLimitingNonce"));?>';
		var actionValue		= "<?php echo esc_attr(get_option('actionRateL'));?>";

		jQuery('#req').val(Rate_request);
		if(actionValue == 0)
		{
			jQuery('#action').val('ThrottleIP');
		}
		else
		{
			jQuery('#action').val('BlockIP');
		}

		
		if(Rate_request !='')
		{	

			var data = {
			'action'					: 'wpns_login_security',
			'wpns_loginsecurity_ajax' 	: 'wpns_waf_rate_limiting_form',
			'Requests'					:  Rate_request,
			'nonce'						:  nonce,
			'rateLValue'				:  rateL,
			'action'					:  actionValue
			};
			jQuery.post(ajaxurl, data, function(response) {
				if(response == '')
				{
					jQuery('#wpns_message').append("<div class= 'notice notice-success is-dismissible' style='mmm  ' >SQL Injection protection is enabled</div>");
					window.scrollTo({ top: 0, behavior: 'smooth' });
				}
				else
				{
					jQuery('#wpns_message').append("notice notice-error is-dismissible' style='mmm  ' >SQL Injection protection is disabled.</div>");
					window.scrollTo({ top: 0, behavior: 'smooth' });
				}
	
			});
		}
		
		
	});
	jQuery('#saveRateL').click(function(){

		var req  	= 	jQuery('#req').val();
		var rateL 	= 	jQuery("input[name='rateL']:checked").val();
		var Action 	= 	jQuery("#action").val();
		var nonce = '<?php echo wp_create_nonce("RateLimitingNonce");?>';
	
		if(req !='' && rateL !='' && Action !='')
		{
			var data = {
			'action'					: 'wpns_login_security',
			'wpns_loginsecurity_ajax' 	: 'wpns_waf_rate_limiting_form',
			'Requests'					:  req,
			'nonce'						:  nonce,
			'rateCheck'					:  rateL,
			'action'					:  Action
			};
			jQuery.post(ajaxurl, data, function(response) {
				if(response == '')
				{
					jQuery('#wpns_message').append("<div class= 'notice notice-success is-dismissible' style='mmm  ' >SQL Injection protection is enabled</div>");
					window.scrollTo({ top: 0, behavior: 'smooth' });
				}
				else
				{
					jQuery('#wpns_message').append("notice notice-error is-dismissible' style='mmm  ' >SQL Injection protection is disabled.</div>");
					window.scrollTo({ top: 0, behavior: 'smooth' });
				}
	
			});
		}
	
	});


</script>

