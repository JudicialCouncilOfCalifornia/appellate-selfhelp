<?php

	echo '<div class="mo_idp_divided_layout mo-idp-full">
            <div class="mo_idp_table_layout mo-idp-center">
                <h2>
                    YOUR PROFILE
                    <span style="float:right;margin-top:-10px;">
                        <input  type="button" 
                                name="remove_accnt" 
                                id="remove_accnt" 
                                class="button button-primary button-large" 
                                value="Remove Account">
                    </span>
                </h2>
                <hr>
                <h4>Here are your Account Details :</h4>
                <table border="1" style="background-color:#FFFFFF; 
                                         border:1px solid #CCCCCC; 
                                         border-collapse: collapse; 
                                         padding:0px 0px 0px 10px; 
                                         margin:2px; width:100%">
                    <tr>
                        <td style="width:45%; padding: 10px;"><b>Registered Email</b></td>
                        <td style="width:55%; padding: 10px;">'.$email.' 
                        </td>
                    </tr>
                    <tr>
                        <td style="width:45%; padding: 10px;"><b>Customer ID</b></td>
                        <td style="width:55%; padding: 10px;">'.$customerID.'</td>
                    </tr>
                    <tr>
                        <td style="width:45%; padding: 10px;"><b>API Key</b></td>
                        <td style="width:55%; padding: 10px;">'.$apiKey.'</td>
                    </tr>
                    <tr>
                        <td style="width:45%; padding: 10px;"><b>Token Key</b></td>
                        <td style="width:55%; padding: 10px;">'.$tokenKey.'</td>
                    </tr>
                </table>
            </div>
           </div>
           <form id="remove_accnt_form" style="display:none;" action="" method="post">';
			    wp_nonce_field( $regnonce );
echo'		<input type="hidden" name="option" value="remove_idp_account" />
		</form>';