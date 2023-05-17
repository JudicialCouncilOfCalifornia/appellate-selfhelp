<?php
class Mowaf_FeedbackHandler
{
    function __construct()
    {
        add_action('admin_init', array($this, 'mo_wpns_feedback_actions'));
    }

    function mo_wpns_feedback_actions()
    {

        global $MowafUtility, $mmp_dirName;

        if (current_user_can('manage_options') && isset($_POST['option'])) {
            switch (sanitize_text_field($_REQUEST['option'])) {
                case "mo_mmp_skip_feedback":
                  $this->wpns_handle_feedback($_POST,false);						
                  break;
                case "mo_mmp_feedback":
                  $this->wpns_handle_feedback($_POST,true);				            
                  break;

            }
        }
    }

    function wpns_handle_skip_feedback($postdata){
        do_action('mo_mmp_show_message',MowafMessages::showMessage('FEEDBACK'),'CUSTOM_MESSAGE');
        deactivate_plugins( dirname(dirname(__FILE__ ))."\\miniorange_firewall_settings.php");
    }

    function wpns_handle_feedback($postdata,$feedback=true)
    {
        if (TEST_MODE == true) {
            do_action('mo_mmp_show_message',MowafMessages::showMessage('FEEDBACK'),'CUSTOM_MESSAGE');
            deactivate_plugins( dirname(dirname(__FILE__ ))."\\miniorange_firewall_settings.php");

        }
        else{
        $user = wp_get_current_user();

        $message = 'Plugin Deactivated';

        $deactivation_reason= isset($_POST['mo_feedback'])? sanitize_text_field($_POST['mo_feedback']):'NA';

        if($deactivation_reason=='other')
            $deactivate_reason_message = array_key_exists('wpns_query_feedback', $_POST) ? sanitize_text_field($_POST['wpns_query_feedback']) : false;
        else
            $deactivate_reason_message = '';


        $reply_required = '';
        
        if (isset($_POST['mo_anonymous_reply']))
            $reply_required = sanitize_text_field($_POST['mo_anonymous_reply']);


        if (!empty($reply_required)) {
            $reply_required = "don't reply";
            $message .= '<b style="color:red";> &nbsp; [Reply :' . esc_attr($reply_required) . ']</b>';
        } else {
            $reply_required = "yes";
            $message .= '&nbsp;[Reply :' . esc_attr($reply_required) . ']';
        }

        if($feedback)
            $message .= ', Feedback: [' .esc_html($deactivation_reason).'] '. esc_attr($deactivate_reason_message) . '';
        else
            $message .= ', Feedback Skipped: [ ' .esc_html($deactivation_reason).' ] '. esc_attr($deactivate_reason_message) . '';

        $message .='<br><i> Plugin Configuration : UpgradeClicked : '.esc_html( get_site_option('wpns_upgrade_button_clicked',0) ).'</i>';


        $email = sanitize_email($_POST['query_mail']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = get_option('mo_wpns_admin_email');
            if (empty($email))
                $email = $user->user_email;
        }
        $phone = get_option('mo_wpns_admin_phone');
        $feedback_reasons = new Mowaf_MocURL();
        global $MowafUtility;
        if (!is_null($feedback_reasons)) {
            if (!$MowafUtility->is_curl_installed()) {
                deactivate_plugins(dirname(dirname(__FILE__ ))."\\miniorange_firewall_settings.php");
                wp_redirect('plugins.php');
            } else {
                $submited = $feedback_reasons->send_email_alert($email, $phone, $message,$feedback);
                if (json_last_error() == JSON_ERROR_NONE) {
                    if (is_array($submited) && array_key_exists('status', $submited) && $submited['status'] == 'ERROR') {
                        do_action('mo_mmp_show_message',$submited['message'],'ERROR');

                    } else {
                        if ($submited == false) {
                            do_action('mo_mmp_show_message','Error while submitting the query.','ERROR');
                        }
                    }
                }

                deactivate_plugins(dirname(dirname(__FILE__ ))."\\miniorange_firewall_settings.php");
                do_action('mo_mmp_show_message','Thank you for the feedback.','SUCCESS');

            }
        }
    }
    }

}new Mowaf_FeedbackHandler();
