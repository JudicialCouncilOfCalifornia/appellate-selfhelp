<?php
echo '
    <div class="mo-idp-visual-tour-overlay" id="mo-idp-overlay" hidden></div>
    <div class="wrap mo-idp-p-3 mo-idp-mr-0 mo-idp-mt-0 mo-idp-margin-left mo-idp-bg-white">';
            echo' <div class="mo-idp-row">
                <div class="mo-idp-col-md-5">
                    <div>
                        <img class="mo-idp-contact-label" src="' . esc_url(MSI_LOGO_URL) . '"> <span class="mo-idp-navbar-head">WP IDP Single Sign On</span>
                    </div>
              </div>';
               echo' <div class="mo-idp-col-md-3">
                    <a class="mo-idp-upgrade-btn mo-idp-btn-free" href="' . esc_url($license_url) . '">Upgrade Now</a>
                </div>';

               echo' <div class="mo-idp-col-md-3 mo-idp-flex">
                    <div id="mo-idp-quicklinks" class="mo-idp-nav-dropdown">
                        <a class="mo-idp-dropdown-btn mo-idp-faq-btn ">Documentation / FAQs <span class="dashicons dashicons-arrow-down-alt2"></span></a>';
                           echo' <div class="mo-idp-dropdown-content">
                                <a href="' . esc_url($help_url) . '" target="_blank">FAQs</a>
                                <a href="' . esc_url($saml_nav_doc) . '" target="_blank">SAML Documentation</a>
                                <a href="' . esc_url($wsfed_nav_doc) . '" target="_blank">WS-Fed Documentation</a>   
                            </div> ';  
                   echo' </div>';
                   echo' <div id="mo-idp-quicklinks">
                        <a class="mo-idp-faq-btn mo-idp-btn-free" href="' . esc_url($support_url) . '">Stuck? Need Help?</a>
                    </div>
                </div>
            </div>
    </div>';

    check_is_curl_installed();

    echo '<div id="tab" class="mo-idp-tab mo-idp-flex nav-tab-wrapper"> ';
              echo'  <div class="mo-idp-header-tab">
                    <a  class="mo-idp-nav-tab 
                        ' . ($active_tab == $idpDashBoardTabDetails->_menuSlug ? 'mo-idp-nav-tab-active' : '') . '" 
                        href="' . esc_url($dashboard_url) . '">
                        ' .esc_attr($idpDashBoardTabDetails->_tabName) . '
                    </a>
                </div>';
             echo' <div class="mo-idp-header-tab">
                <a  class="mo-idp-nav-tab 
                    ' . ($active_tab == $spSettingsTabDetails->_menuSlug ? 'mo-idp-nav-tab-active' : '') . '" 
                    href="' . esc_url($idp_settings) . '">
                    ' .esc_attr($spSettingsTabDetails->_tabName) . '
                </a>
            </div>';
            echo'<div class="mo-idp-header-tab">
                <a  class="mo-idp-nav-tab 
                    ' . ($active_tab == $metadataTabDetails->_menuSlug ? 'mo-idp-nav-tab-active' : '') . '" 
                    href="' . esc_url($sp_settings) . '">
                    ' . esc_attr($metadataTabDetails->_tabName) . '
                </a>
            </div>';
            echo'<div class="mo-idp-header-tab">
                <a class="mo-idp-nav-tab 
                    ' . ($active_tab == $attrMapTabDetails->_menuSlug ? 'mo-idp-nav-tab-active' : '') . '" 
                    href="' . esc_url($attr_settings) . '">
                    ' . esc_attr($attrMapTabDetails->_tabName) . '
                </a>
            </div>';
            echo'<div class="mo-idp-header-tab">
                <a  class="mo-idp-nav-tab 
                    ' . ($active_tab == $settingsTabDetails->_menuSlug ? 'mo-idp-nav-tab-active' : '') . '" 
                    href="' . esc_url($login_settings) . '">
                    ' . esc_attr($settingsTabDetails->_tabName) . '
                </a>
            </div>';
            echo'<div class="mo-idp-header-tab">
                <a class="mo-idp-nav-tab
                    ' . ($active_tab == $demoRequestTabDetails->_menuSlug ? 'mo-idp-nav-tab-active' : '') . '"
                    href="' . esc_url($demoRequest_url) . '">
                    ' . esc_attr($demoRequestTabDetails->_tabName) . '
                </a>
            </div>';
            echo'<div class="mo-idp-header-tab">
                <a class="mo-idp-nav-tab
                    '.($active_tab == $idpAddonsTabDetails->_menuSlug ? 'mo-idp-nav-tab-active' : '').'"
                    href="'. esc_url($idpAddons_url) .'">
                    '.esc_attr($idpAddonsTabDetails->_tabName).'
                </a>
            </div>';
            echo'<div class="mo-idp-header-tab">
                <a class="mo-idp-nav-tab 
                    ' . ($active_tab == $licenseTabDetails->_menuSlug    ? 'mo-idp-nav-tab-active' : '') . '" 
                    href="' . esc_url($license_url) . '">
                    ' . esc_attr($licenseTabDetails->_tabName) . '
                </a>
            </div>';
            echo'<div class="mo-idp-header-tab">
                <a class="mo-idp-nav-tab 
                    ' . ($active_tab == $profileTabDetails->_menuSlug ? 'mo-idp-nav-tab-active' : '') . '" 
                    href="' . esc_url($register_url) . '">
                    ' . esc_attr($profileTabDetails->_tabName) . '
                </a>
            </div>';
   echo' </div>';

if (!get_site_option("mo_idp_new_certs"))
    echo "<div style='display:block; width:62%; margin:auto; margin-top:10px; color:black; background-color:rgba(251, 232, 0, 0.15); 
    padding:0.938rem; border:solid 1px rgba(204, 204, 0, 0.36); font-size:large; line-height:normal'>
    <span style='color:red;'><span class='dashicons dashicons-warning'></span> <b>WARNING</b>:</span> The existing certificates have expired. Please update the certificates ASAP to secure your SSO.<br> Go to the <a href='admin.php?page=idp_metadata'><b>IDP Metadata</b></a> tab
    of the plugin to update your certificates. Make sure to update your Service Provider with the new certificate to ensure your SSO does not break.
</div>";
