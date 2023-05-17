<?php 
	
	global $MowafUtility,$mmp_dirName;
	$mo_wpns_handler 	= new MowafHandler();

	if(current_user_can( 'manage_options' )  && isset($_POST['option']))
	{
		switch(sanitize_text_field($_POST['option']))
		{
			case "mo_wpns_manual_block_ip":
				mmp_handle_manual_block_ip(filter_var($_POST['IP']));			break;
			case "mo_wpns_unblock_ip":
				mmp_handle_unblock_ip(sanitize_text_field($_POST['id']));			break;
			case "mo_wpns_whitelist_ip":
				mmp_handle_whitelist_ip(filter_var($_POST['IP']));				break;
			case "mo_wpns_remove_whitelist":
				mmp_handle_remove_whitelist(sanitize_text_field($_POST['id'] ));	break;
		}
	}

	$blockedips 		= $mo_wpns_handler->get_blocked_ips();
	$whitelisted_ips 	= $mo_wpns_handler->get_whitelisted_ips();
	$img_loader_url		= plugins_url('Web-Application-Firewall/includes/images/loader.gif');
	$page_url			= "";
	$license_url		= add_query_arg( array('page' => 'mo_mmp_upgrade'), sanitize_text_field($_SERVER['REQUEST_URI'] ));

	//include $dirName . 'views/ip-blocking.php';

/** IP BLOCKING RELATED FUNCTIONS **/

	// Function to handle Manual Block IP form submit
	function mmp_handle_manual_block_ip($ip)
	{
		
		global $MowafUtility;	

		if( $MowafUtility->check_empty_or_null( $ip) )
		{
			echo("empty IP");
			exit;
		} 
		if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/',$ip))
		{
			echo("INVALID_IP_FORMAT");
			exit;
		}
		else
		{
			$ipAddress 		= sanitize_text_field( $ip );
			$mo_wpns_config = new MowafHandler();
			$isWhitelisted 	= $mo_wpns_config->is_whitelisted($ipAddress);
			if(!$isWhitelisted)
			{
				if($mo_wpns_config->is_ip_blocked($ipAddress)){
					
					echo("already blocked");	
					exit;
				} else{
					$mo_wpns_config->block_ip($ipAddress, MowafConstants::BLOCKED_BY_ADMIN, true);
					//do_action('mo_mmp_show_message',MowafMessages::showMessage('IP_PERMANENTLY_BLOCKED'),'SUCCESS');
					//not in structures		
					?>
					<table id="blockedips_table1" class="display">
				<thead><tr><th>IP Address&emsp;&emsp;</th><th>Reason&emsp;&emsp;</th><th>Blocked Until&emsp;&emsp;</th><th>Blocked Date&emsp;&emsp;</th><th>Action&emsp;&emsp;</th></tr></thead>
				<tbody>
<?php					
				$mo_wpns_handler 	= new MowafHandler();
				$blockedips 		= $mo_wpns_handler->get_blocked_ips();
				$whitelisted_ips 	= $mo_wpns_handler->get_whitelisted_ips();
				global $mmp_dirName;
				foreach($blockedips as $blockedip)
				{
	echo 			"<tr class='mo_wpns_not_bold'><td>".$blockedip->ip_address."</td><td>".$blockedip->reason."</td><td>";
					if(empty($blockedip->blocked_for_time)) 
	echo 				"<span class=redtext>Permanently</span>"; 
					else 
	echo 				esc_attr(gmdate("M j, Y, g:i:s a",$blockedip->blocked_for_time));
	echo 			"</td><td>".esc_attr(gmdate("M j, Y, g:i:s a",$blockedip->created_timestamp))."</td><td><a  onclick=unblockip('".esc_attr($blockedip->id)."')>Unblock IP</a></td></tr>";
				} 
	?>
					</tbody>
					</table>
					<script type="text/javascript">
						jQuery("#blockedips_table1").DataTable({
						"order": [[ 3, "desc" ]]
						});
					</script>
					<?php
					exit;
				}
			}
			else
			{
				
				echo("IP_IN_WHITELISTED");
				exit;
			}
		}
	}


	// Function to handle Manual Block IP form submit
	function mmp_handle_unblock_ip($entryID)
	{
		global $MowafUtility;
		
		if( $MowafUtility->check_empty_or_null($entryID))
		{
			
			echo("UNKNOWN_ERROR");
			exit;
		}
		else
		{
			$entryid 		= sanitize_text_field($entryID);
			$mo_wpns_config = new MowafHandler();
			$mo_wpns_config->unblock_ip_entry($entryid);
			
					?>
				<table id="blockedips_table1" class="display">
				<thead><tr><th>IP Address&emsp;&emsp;</th><th>Reason&emsp;&emsp;</th><th>Blocked Until&emsp;&emsp;</th><th>Blocked Date&emsp;&emsp;</th><th>Action&emsp;&emsp;</th></tr></thead>
				<tbody>
<?php					
				$mo_wpns_handler 	= new MowafHandler();
				$blockedips 		= $mo_wpns_handler->get_blocked_ips();
				$whitelisted_ips 	= $mo_wpns_handler->get_whitelisted_ips();
				global $mmp_dirName;
				foreach($blockedips as $blockedip)
				{
	echo 			"<tr class='mo_wpns_not_bold'><td>".esc_attr($blockedip->ip_address)."</td><td>".esc_attr($blockedip->reason)."</td><td>";
					if(empty($blockedip->blocked_for_time)) 
	echo 				"<span class=redtext>Permanently</span>"; 
					else 
	echo 				esc_attr(gmdate("M j, Y, g:i:s a",$blockedip->blocked_for_time));
	echo 			"</td><td>".esc_attr(gmdate("M j, Y, g:i:s a",$blockedip->created_timestamp))."</td><td><a onclick=unblockip('".$blockedip->id."')>Unblock IP</a></td></tr>";
				} 
	?>
					</tbody>
					</table>
					<script type="text/javascript">
						jQuery("#blockedips_table1").DataTable({
						"order": [[ 3, "desc" ]]
						});
					</script>
					<?php
			
			exit;
		}
	}


	// Function to handle Whitelist IP form submit
	function mmp_handle_whitelist_ip($ip)
	{
		global $MowafUtility;
		if( $MowafUtility->check_empty_or_null($ip))
		{
			echo("EMPTY IP");
			exit;
		}
		if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/',$ip))
		{			//change message
				echo("INVALID_IP");
				exit;
		}
		else
		{
			$ipAddress = sanitize_text_field($ip);
			$mo_wpns_config = new MowafHandler();
			if($mo_wpns_config->is_whitelisted($ipAddress))
			{
				echo("IP_ALREADY_WHITELISTED");
				exit;
			}
			else
			{
				$mo_wpns_config->whitelist_ip($ip);
				$mo_wpns_handler 	= new MowafHandler();
				$whitelisted_ips 	= $mo_wpns_handler->get_whitelisted_ips();
					
			?>
				<table id="whitelistedips_table1" class="display">
				<thead><tr><th >IP Address</th><th >Whitelisted Date</th><th >Remove from Whitelist</th></tr></thead>
				<tbody>
				<?php
					foreach($whitelisted_ips as $whitelisted_ip)
					{
						echo "<tr class='mo_wpns_not_bold'><td>".esc_html($whitelisted_ip->ip_address)."</td><td>".date("M j, Y, g:i:s a",esc_html($whitelisted_ip->created_timestamp))."</td><td><a  onclick=removefromwhitelist('".$whitelisted_ip->id."')>Remove</a></td></tr>";
					} 

	
				?>
				</tbody>
				</table>
			<script type="text/javascript">
				jQuery("#whitelistedips_table1").DataTable({
				"order": [[ 1, "desc" ]]
				});
			</script>

	<?php
			exit;
			}
		}
	}


	// Function to handle remove whitelisted IP form submit
	function mmp_handle_remove_whitelist($entryID)
	{
		global $MowafUtility;
		if( $MowafUtility->check_empty_or_null($entryID))
		{
			echo("UNKNOWN_ERROR");
			exit;
		}
		else
		{
			$entryid = sanitize_text_field($entryID);
			$mo_wpns_config = new MowafHandler();
			$mo_wpns_config->remove_whitelist_entry($entryid);
				$mo_wpns_handler 	= new MowafHandler();
				$whitelisted_ips 	= $mo_wpns_handler->get_whitelisted_ips();
					
			?>
				<table id="whitelistedips_table1" class="display">
				<thead><tr><th >IP Address</th><th >Whitelisted Date</th><th >Remove from Whitelist</th></tr></thead>
				<tbody>
			<?php
					foreach($whitelisted_ips as $whitelisted_ip)
					{
						echo "<tr class='mo_wpns_not_bold'><td>".esc_html($whitelisted_ip->ip_address)."</td><td>".date("M j, Y, g:i:s a",esc_html($whitelisted_ip->created_timestamp))."</td><td><a onclick=removefromwhitelist('".$whitelisted_ip->id."')>Remove</a></td></tr>";
					} 

	
			?>
				</tbody>
				</table>
			<script type="text/javascript">
				jQuery("#whitelistedips_table1").DataTable({
				"order": [[ 1, "desc" ]]
				});
			</script>

		<?php
			exit;
		}
	}

	