<?php
	echo'<div class="mo_idp_table_layout">
	        <div style="float:left"><h3>List of Service Providers</h3></div>
			<div style="float:right;margin:1.3em 1em">Number of remaining SSO users: <b>'.($allowed - $users).'</b> </div>
			<div style="float:right;margin:1.3em 0">Number of SPs remaining: <b>'.$remaining.'</b> </div>
			<table class="sp_table">
				<tr>
					<th>SP Name</th>
					<th>SP Issuer</th>
					<th>Protocol</th>
					<th>Actions</th>
				</tr>';
			foreach($sp_list as $sp)
			{
				$test_window = site_url(). '/?option=testConfig'.
                                            '&acs='.$sp->mo_idp_acs_url.
                                            '&issuer='.$sp->mo_idp_sp_issuer.
                                            '&defaultRelayState='.$sp->mo_idp_default_relayState;
	echo		'<tr>
					<td style="min-width: 113px;height:45px">'.$sp->mo_idp_sp_name.'</td>
					<td style="max-width: 315px;height:45px">'.$sp->mo_idp_sp_issuer.'</td>
					<td style="max-width: 315px;height:45px">'.$sp->mo_idp_protocol_type.'</td>
					<td>
						<a href="#" onclick="showTestWindow(\''.$test_window.'\');">Test</a> | 
						<a href="'.$settings_url.$sp->id.'">Edit</a> | 
						<a href="'.$delete_url.$sp->id.'">Delete</a>
					</td>
				</tr>';
			}
	echo	'</table>
			<br>
			<a href="'.$add_sp_url.'"><input type="button" value="Add New SP" class="button button-primary button-large"></a>
		</div>';