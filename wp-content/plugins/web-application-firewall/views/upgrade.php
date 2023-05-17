<?php

echo '
<style>
/* upgrade page */

.mo_wpns_pricing_layout {
  margin: 10px;
  background-color: #ffffff;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding-bottom: 10px;
  min-width: 33%;
  border: 1px solid #c3c4c7;
  border-top: none;
  border-radius: 10px;
  position: relative;
  box-shadow:0 0px 0px 0 rgba(0, 0, 0, 0.2), 0 6px 10px 0 rgba(0, 0, 0, 0.19);
}

.mo2f_pricing {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}
.mo2f_price {
  /* width: 100%; */
  font-size: 40px;
  padding: 5px;
  margin: 10px;
  margin-bottom: 10px;
  display: flex;
  align-items: top;
  gap: 10px;
  /* background-color: #fff; */
  padding: 20px;
  /* border: 2px solid #2271b1; */
  text-align: center;
  justify-content: center;
  font-weight: 600;
  border-radius: 10px;
}

#mo2f_before_offer_price {
  text-decoration: line-through;
  color: red;
  font-size: 20px;
}

#mo2f_offer_price {
  color: #fff;
  font-size: 40px;
}

.mo2f_pricing_container {
  display: flex;
  justify-content: center;
  align-items: center;
}
.mo_wpns_price_select{
  padding:0 30px !important;
  margin-bottom:10px;
}
.mo_wpns_circle_background {
  display: flex;
  align-items: center;
  flex-direction: column;
  padding: 10px;
  width: 100%;
  height: 30%;
  background-color: #2271b1;
  color: #fff !important;
  border-radius: 0 0 50% 50%;
  margin-bottom: 10px;
}

.mo_wpns_circle_background h1 {
  color: #fff;
}

.mo2f_feature_list {
  width: 100%;
  text-align: center;
}
.mo2f_feature_list li {
  background-color: #fff;
  width: 100%;
  flex: none;
  padding: 5px 0;
  font-weight: bold;
}
.mo2f_feature_list li:nth-child(odd) {
  background-color: #eee;
}
.mo2f_feature_list li:nth-child(even) {
  background-color: #fff;
}

.mo_wpns_ribbon {
  -webkit-transform: rotate(-45deg); 
    -moz-transform: rotate(-45deg); 
    -ms-transform: rotate(-45deg); 
    -o-transform: rotate(-45deg); 
    transform: rotate(-45deg); 
    border: 28px solid transparent;
    border-bottom: 28px solid #757575;
    position: absolute;
    top: -3px;
    left: -60px;
    padding: 0 10px;
    width: 120px;
    color: white;
    font-family: sans-serif;
    size: 11px;
    border-bottom-color: dodgerblue;
}

.mo_wpns_ribbon .txt {
    position: absolute;
    bottom: -22px;
    left: 20px;
}

.mo_wpns_pricing_layout strong{
  font-size: 25px;
  margin: 5px 0;
}​
</style>
<br>
<div class="mo2f_pricing_container">
				<div class="mo_wpns_pricing_layout" id="mo_all_in_one">
					<div class="mo_wpns_circle_background">
					<br>
					<strong>All in One</strong>
					<strong>Security</strong>
					<br>
					Price starting form
					<div class="mo2f_price">
						<div id="mo2f_offer_price">$95</div>
					</div>
					<div style="text-align:center">
            <select class="mo_wpns_price_select" id="mo_wpns_price">
              <option value="95">1 site</option>
              <option value="180">upto 5 sites</option>
              <option value="290">upto 10 sites</option>
            </select>
          </div>
				</div>
				<a target="_blank" class="button button-primary" onclick="wpns_upgrade(\'wp_security_premium_plan\')">Upgrade Now</a>
				<div class="mo2f_feature_list" id="hide_all_in_one">
					<ul>
						<li> Application Firewall </li>
						<li> Malware scanner </li>
						<li> Login and Spam protection </li>
						<li> Advanced Rate Limiting </li>
						<li> Real-Time IP Blocklist</li>
						<li> Country Blocking</li>
						<li> Crawlers and Bot detection</li>
						<li> Plugin/Theme Vulnerability Monitoring</li>
						<li> File Change Detection </li>
						<li> Login Security - RECAPTCHA </li>
						<li> Investigation and Malware Removal</li>
						<li> Web Encrypted Backup and Recovery </li>	
						<li> We Monitor your Site Security</li>
						<li> Premium Customer Support</li>
						<li> <a href="https://plugins.miniorange.com/wp-security-pro#pricing" target="_blank">More...<a> </li>
					</ul>
				</div>
			</div>	
	
';
echo '<form class="plan_redirect" id="wpns_loginform"
                  action="https://login.xecurify.com/moas/login"
                  target="_blank" method="post" style="display:none;">
                <input type="email" name="username" value="' . esc_attr( get_option( "mo_wpns_admin_email" ) ) . '"/>
                <input type="text" name="redirectUrl"
                       value="https://login.xecurify.com/moas/initializepayment"/>
                <input type="text" name="requestOrigin" id="requestOrigin"/>
            </form>
            
            <form class="registration_redirect" id="wpns_registration_form"
                  action="' . esc_url( $profile_url ) . '"
                 method="post" style="display:none;">
                
            </form>
            
            ';

$iscustomervalid = MowafUtility::icr();
echo '<script>
      jQuery("#mo_wpns_price").change((e)=>{
        jQuery("#mo2f_offer_price").html("$"+e.target.value);
      })
      var iscustomervalid = ' . esc_attr( $iscustomervalid ) . ';
      var nonce 		= "'.wp_create_nonce("wpns-upgrade-button").'";

      function wpns_upgrade(plan){
        
          var data = {
            "action"					: "wpns_login_security",
            "wpns_loginsecurity_ajax" : "wpns_upgrade_button",
            "nonce"						:  nonce,
            };

          jQuery.post(ajaxurl, data, function(response) {
          });

          if(iscustomervalid){
            jQuery(\'#requestOrigin\').val(plan);
            jQuery(\'#wpns_loginform\').submit();//wpns_registration_form
          }else{
            jQuery(\'#wpns_registration_form\').submit();//wpns_registration_form
          }
        
    }
    </script>';


?>
