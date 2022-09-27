<?php
	echo '  
        <div class="mo-idp-divided-layout mo-idp-full mo-idp-bg mo-idp-margin-left mo-idp-pt">
            <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width" style="transform:translateX(10px);">';
        echo'    <a href="'.esc_url($license_url).'" class="mo-idp-upload-data-anchor">';
         echo'      <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width mo-idp-border mo-idp-signin-box" style="margin-top:3rem !important;">';
            echo'        <img src="'.MSI_LOCK.'" class="mo-idp-signin-lock"/>   ';
             echo'           <div class="mo-idp-signin-opacity">   
                            <h2 class="mo-idp-text-center mo-idp-entity-info" >Sign In Options</h2>
                            <hr class="mo-idp-add-new-sp-hr">
                                <div class="mo-idp-mt-5 mo-idp-text-color" >
                                    <span class="mo-idp-home-card-link"><b>Add the following shortcode to a page:</b></span> <span class="mo-idp-help-desc  mo-idp-note mo-idp-ml-5">This is available in the Premium version.</span>  
                                </div>       
                        </div>
                    </div>

                    <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width mo-idp-border mo-idp-signin-box">';
                 echo'   <img src="'.MSI_LOCK.'" class="mo-idp-signin-lock" />  '; 
                   echo'     <div class="mo-idp-signin-opacity">   
                                <h2 class="mo-idp-text-center mo-idp-entity-info" >Custom Login Page URL</h2>
                                <hr class="mo-idp-add-new-sp-hr">
                                    <div class="mo-idp-mt-5 mo-idp-text-color">
                                    <span class="mo-idp-home-card-link"><b>Enter Custom Login Page URL :</b></span><span class="mo-idp-note mo-idp-help-desc" style="margin-left:5.5rem;">This is available in the Premium version.</span>
                                    </div>
                        </div>
                    </div>

                    <div class="mo-idp-table-layout mo-idp-center mo-idp-sp-width mo-idp-border mo-idp-signin-box" style="margin-bottom:2rem"> ';
                   echo' <img src="'.MSI_LOCK.'" class="mo-idp-signin-lock"/>';
                   echo'     <div class="mo-idp-signin-opacity">   
                            <h2 class="mo-idp-text-center mo-idp-entity-info" >Role Based Restriction</h2>
                            <hr class="mo-idp-add-new-sp-hr">
                                <form style="font-size:0.97rem;" class="mo-idp-sp-data-table mo-idp-text-color">
                                <input type="checkbox" disabled class="mo-idp-pricing-text" >
                                Check this option if you want to implement role based SSO. Choose the roles for which you want SSO to be enabled in your configured Service Providers.
                                </form>
                        </div>
                    </div>
                    </a> 
             </div>
        </div>
      
    ';





