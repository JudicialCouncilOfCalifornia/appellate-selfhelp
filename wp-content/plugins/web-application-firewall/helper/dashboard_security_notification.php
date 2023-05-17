<?php

class Mowaf_miniorange_security_notification{


        function my_custom_dashboard_widgets() {
            global $wp_meta_boxes;

            wp_add_dashboard_widget('custom_help_widget', 'MiniOrange Website Security',array($this, 'custom_dashboard_help'));
     }

	    function custom_dashboard_help() {
		       global $wpdb,$type_of_scan,$total_scanned_files,$wpnsDbQueries;
           
           $array = $wpdb->get_results("SELECT MAX(id) as id FROM ".$wpdb->base_prefix.'wpns_malware_scan_report');

           $latest_id = (int)$array[0]->id;

          $last_scan_malicious_count = $wpdb->get_results("SELECT COUNT(*) as total FROM ".$wpdb->base_prefix."wpns_malware_scan_report_details where report_id=".$latest_id);
          

           $total_malicious_count = $wpdb->get_results("SELECT COUNT(*) as total FROM ".$wpdb->base_prefix."wpns_malware_scan_report_details");
           
            $table_content =  $wpdb->get_results("SELECT * FROM ".$wpdb->base_prefix."wpns_malware_scan_report where id=".$latest_id);
            if(sizeof($table_content)>0){
             $type_of_scan = $table_content[0]->scan_mode;
             $total_scanned_files = $table_content[0]->scanned_files;
           }
           if($type_of_scan === null)
            $type_of_scan ='Not Scanned Yet';
           

           if($total_scanned_files === null)
            $total_scanned_files = '0';
           
          
           

          echo "<html>
                <head>
                <style>
                

              p{
              margin:0px;
             
              </style>
              </head>
             
     <div style='width:100%;background-color:#555f5f;padding-top:10px;''>
          <div style='font-size:25px;color:white;text-align:center'>
          <strong style='font-weight:300;''>Last Scan Result <span style='color:orange;'>[". esc_attr($type_of_scan)." ]</span></strong>
      </div>
       <hr>
       <div>
        <table>
                <tbody>
                       
                            <tr>
                              <td style='border-collapse:collapse!important;color:#0a0a0a;line-height:1.3;margin:0;padding:0;text-align:left;vertical-align:top;word-wrap:normal'>
                                  <table dir='ltr'   style='table-layout:fixed;margin:10px 0 20px 0;padding:0;vertical-align:top;width:100%'>
                                    <tbody>
                                     

                                      <tr>
                                      <td style='text-align:center;font-size:36px;color:#ffffff;font-weight:400' ><strong>".esc_attr($last_scan_malicious_count[0]->total)."</strong></td>
                                      <td style='text-align:center;font-size:36px;color:#ffffff;font-weight:400'><strong>".esc_attr($total_malicious_count[0]->total)."</strong></td>
                                       

                                       <td style='text-align:center;font-size:36px;color:#ffffff;font-weight:400'><strong>".esc_attr($total_scanned_files)."</strong></td>
                                     
                                   
                                      </tr>
                                   
                                      <tr><td>&nbsp;</td><td></td></tr>
                                      <tr>
                                      <td style='font-size:18px;color:#ffffff;text-align:center'><strong style='font-weight:300;'>Current Infected Files </strong></td>
                                       <td style='font-size:18px;color:#ffffff;text-align:center'><strong style='font-weight:300;'>Total Infected Files Found</strong></td>
                                     
                                       <td style='font-size:18px;color:#ffffff;text-align:center'><strong style='font-weight:300;'>Total Files Scanned</strong></td>
                                   
                                      </tr>
                                    </tbody>
                                    </table>
                              </tr>  
                       </tbody>
               </table>
       </div>
     </div>

";
            echo '<a class="button button-primary" style="background-color:#f0a702;width:100%;text-align:center;" href="admin.php?page=mo_mmp_malwarescan&tab=default&view='.esc_attr($latest_id).'"><h3 style="background-color:#f0a702">View Details</h3></a>';     
         echo '<br><br>';


              
             
			}
}

?>