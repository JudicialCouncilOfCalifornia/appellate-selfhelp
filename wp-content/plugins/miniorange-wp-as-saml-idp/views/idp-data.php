<?php
echo 	'<div class="mo_idp_divided_layout mo-idp-full">
            <div class="mo_idp_table_layout mo-idp-center">
                <h2>
                    IDP METADATA';
                    restart_tour();
        echo    '</h2><hr>
                <h4>You will need the following information to configure your Service Provider. Copy it and keep it handy:</h4>
                <form name="f" method="post" action="" id="mo_idp_settings">
                    <input type="hidden" name="option" value="mo_idp_entity_id" />
                    <table>
                        <tr>
                            <td style="width:20%">IdP EntityID / Issuer:</td>
                            <td>
                                <input  type="text" 
                                        name="mo_saml_idp_entity_id" 
                                        placeholder="Entity ID of your IdP" 
                                        style="width: 95%;" 
                                        value="'.$idp_entity_id.'" 
                                        required="">
                            </td>
                            <td style="width:17%">
                                <input  type="submit" 
                                        name="submit" 
                                        style="width:100px;" 
                                        value="Update" 
                                        class="button button-primary button-large">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div class="mo_idp_note">
                                    <i>
                                        <span style="color:red"><b>Note:</b></span> 
                                        If you have already shared the below URLs or Metadata with your SP, 
                                        do <b>NOT</b> change IdP EntityID. It might break your existing login flow.
                                    </i>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
                <table border="1" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px; border-collapse: collapse; width:98%" id = "idpInfoTable">
                    <tr>
                        <td style="width:40%; padding: 15px;"><b>IDP-EntityID / Issuer</b></td>
                        <td style="width:60%; padding: 15px;">'.$idp_entity_id.'</td>
                    </tr>
                    <tr>
                        <td style="width:40%; padding: 15px;"><b>SAML Login URL / Passive Login URL</b></td>
                        <td style="width:60%;  padding: 15px;">'.$site_url.'</td>
                    </tr>
                    <tr>
                        <td style="width:40%; padding: 15px;"><b>SAML Logout URL / WS-FED Logout URL</b></td>
                        <td style="width:60%;  padding: 15px;">'.$site_url.'</td>
                    </tr>
                    <tr>
                        <td style="width:40%; padding: 15px;"><b>Certificate (Optional)</b></td>';
echo					'<td style="width:60%;  padding: 15px;"><a href="'.$certificate_url.'" download>Download</a></td>';
echo			'    </tr>
                    <tr>
                        <td style="width:40%; padding: 15px;"><b>Response Signed</b></td>
                        <td style="width:60%;  padding: 15px;">You can choose to sign your response in <a href="'.$idp_settings.'">Identity Provider</a></td>
                    </tr>
                    <tr>
                        <td style="width:40%; padding: 15px;"><b>Assertion Signed</b></td>
                        <td style="width:60%;  padding: 15px;">You can choose to sign your assertion in <a href="'.$idp_settings.'">Identity Provider</a></td>
                    </tr>
                </table>
                <p style="text-align: center;font-size: 13pt;font-weight: bold;">OR</p>
                <p>You can provide this metadata URL to your Service Provider</p>
                <div id="metadataXML" class="mo_idp_note">
                    <b><a target="_blank" href="'.$metadata_url.'">'.$metadata_url.'</a></b>
                </div>
            </div>
            <div class="mo_idp_table_layout mo-idp-center">
                <h4>You can run the following command to set WS-FED as your Authentication Protocol for your Federated Domain:</h4>
                <div class="mo_idp_note" style="color:red">
                    NOTE: Make sure to replace `&lt;your_domain&gt;` with your domain before running the command. 
                </div>
                <div class="copyClip" style="float:right;"> 
                    <h4> Copy to ClipBoard <span class="dashicons dashicons-admin-page" > </span> </h4> 
                </div>
                <textarea style="width:100%;height:100px;font-family:monospace;" class="copyBody">'.$wsfed_command.'</textarea>
            </div>
          </div>';
