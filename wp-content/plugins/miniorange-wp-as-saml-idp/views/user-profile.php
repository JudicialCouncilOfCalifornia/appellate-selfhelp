<?php

	echo '<div class="mo-idp-divided-layout mo-idp-full mo-idp-bg mo-idp-margin-left mo-idp-pt">
            <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width">
                <h2 class="mo-idp-add-new-sp">
                    Your Profile
                </h2>
                <hr class="mo-idp-add-new-sp-hr">
                <h4 class="mo-idp-home-card-link">Here are your Account Details :</h4>
                <table class="mo-idp-home-card-link mo-idp-table-input wp-idp-pricing-down"  style="border-collapse: collapse;">';
                echo '
                    <tr>
                        <td class="mo-idp-profile-table"><b>Registered Email</b></td>
                        <td class="mo-idp-profile-table">'.esc_attr($email).' 
                        </td>
                    </tr>';
                    echo '
                    <tr>
                        <td class="mo-idp-profile-table"><b>Customer ID</b></td>
                        <td class="mo-idp-profile-table">'.esc_attr($customerID).'</td>
                    </tr>';
                    echo '
                </table>
                <div class="mo-idp-mt-5 mo-idp-flex">
                        <input  type="button" 
                                name="remove_accnt" 
                                id="remove_accnt" 
                                class="button mo-idp-button-large mo-idp-text-white"  style="background-color:#2271B1;"
                                value="Remove Account">
                </div>
            </div>
        </div>
           <form id="remove_accnt_form" style="display:none;" action="" method="post">';
			    wp_nonce_field( $regnonce );
echo'		<input type="hidden" name="option" value="remove_idp_account" />
		</form>';