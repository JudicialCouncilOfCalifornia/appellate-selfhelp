<?php

echo '
<div class="mo-idp-bg mo-idp-divided-layout mo-idp-full mo-idp-margin-left">
    <div class="mo-idp-flex mo-idp-home mo-idp-p-3">
        <div class="mo-idp-home-row1">';
        /** @var \IDP\Helper\Utilities\PluginPageDetails $tabs */
            $count = 0 ;
            $class = "";
            $start="";
            foreach($tabDetails as $tabs){
                if($count == 4)
                    break;
                $link = add_query_arg(array('page' => $tabs->_menuSlug), esc_url_raw($_SERVER['REQUEST_URI']));
                if($count == 0)
                {
                    $class = "mo-idp-start-here-card";
                    $function1 = "mo_idp_dashboard_start()";
                    $head = "Start here" ;
                    $tabs ->_tabName = "Configure Service Provider";
                    $imgBg = "mo-idp-img-bg";
                    $href="mo-idp-home-card-link-href";
                }
                else{
                    $class = "mo-idp-home-card" ;
                    $function1 = "mo_idp_dashboard_rest()";
                    $head = "Go there";
                    $start = "";
                    $imgBg = "";
                    $href="mo-idp-home-card-link-href-rest";
                }
                echo '
                    <div class="'.$class.'" onclick="location.href=\''.esc_url($link).'\'">
                        <div class="mo-idp-home-flex">
                            <div class="'.$imgBg.'">
                                <img class="mo-idp-img-size"  src="';
                                    echo MSI_URL. 'includes/images/'.esc_attr($tabs->_menuSlug).'.png'; echo '" />
                            </div>
                            <span class="mo-idp-home-card-head addon-table-list-status">'. esc_attr($tabs->_tabName) .'</span>
                        </div>
                        <p class="mo-idp-home-card-desc addon-table-list-name" >'. esc_attr($tabs->_description) .'</p>
                        <a class="'.$href.'" href="' . esc_url($link) . '"> '.esc_attr($head).' &#8594 </a>
                    </div>
                ';
                $count++;    
            }
    echo '            
    </div>

    <div class="mo-idp-home-advt mo-idp-mt-5" >';
        echo'';
        echo'<div class="mo-idp-home-advt-integration">
            <h2 style="color:#01316D;margin-bottom:2rem;" class="mo-idp-text-center mo-idp-home-card-link mo-idp-mt-0">Supported Integrations</h2>';
            $count = 0 ;
            foreach($IntegrationsDetials as $integrationName=>$IntegrationImage){
                if($count % 3 == 0){
                    echo '
                    <div class="mo-idp-flex mo-idp-flex-integration">
                        <div class="mo-idp-logo-saml-cstm">
                        <a href="'. esc_url($idpAddons_url) .'" class="mo-idp-upload-data-anchor">
                            <img class="mo-idp-dashboard-logo" src="'.esc_url($IntegrationImage).'"/>
                            <p class="mo-idp-home-card-desc mo-idp-text-center" id="idp_entity_id" >'.esc_attr($integrationName).'</p>
                        </a>
                        </div>
                    
                    ';
                }else{
                echo '    
                    <div class="mo-idp-logo-saml-cstm">
                        <a href="'. esc_url($idpAddons_url) .'" class="mo-idp-upload-data-anchor">
                            <img class="mo-idp-dashboard-logo" src="'.esc_url($IntegrationImage).'"/>
                            <p class="mo-idp-home-card-desc mo-idp-text-center" id="idp_entity_id">'.esc_attr($integrationName).'</p>
                            </a>
                        </div>
                    
                    ';
                }
                $count++;
                if($count % 3 == 0){
                    echo '</div>';
                }
            }
            
   echo     '  
        </div>        
    </div>
</div>
        
    
</div>
';


