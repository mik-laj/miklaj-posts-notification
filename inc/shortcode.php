<?php

class Miklaj_Notification_Shortcode {
    function __construct() {
        $this->register_listener();
    }

    function register_listener(){
        add_action( 'admin_post_nopriv_miklaj_notification_save_form', array($this, 'save_form' ));
        add_action( 'admin_post_miklaj_notification_save_form', array($this, 'save_form' ) );
        add_shortcode( 'notification-form', array($this, 'shortcode_form' ) );
    }

    function save_form() {
        if(
            !isset($_POST['name']) ||
            !isset($_POST['email']) ||
            !isset($_POST['taxonomy']) ||
            !isset($_POST['frequency']) ||
            !isset($_POST['personal_data'])
            )
        {
            wp_redirect( home_url() ); exit;
        }
        $name = $_POST['name'];
        $email = $_POST['email'];
        $taxonomy = $_POST['taxonomy'];
        $frequency = $_POST['frequency'];
        $personal_data = $_POST['personal_data'] === 'on';

        $frequency_allowed_keys = array_keys($GLOBALS['mik_laj_notification']->frequencies);
        if(
            empty($name) ||
            empty($email) ||
            empty($taxonomy) ||
            !in_array($frequency, $frequency_allowed_keys)){
            echo '3';
            // wp_redirect( home_url() );
            exit;
        }
        if(!$personal_data){
            echo '4';
            wp_redirect( home_url() );
            exit;
        }
        $this->save_subscription($name, $email, $taxonomy, $frequency);
        wp_redirect( home_url() );
    }

    function save_subscription($name, $email, $taxonomy, $frequency){
        global $wpdb;
        $last_send = current_time( 'mysql' );
        $taxonomy = serialize($taxonomy);
        $wpdb->insert(
            $wpdb->prefix.'miklaj_notification',
            compact('name', 'email', 'taxonomy', 'frequency', 'last_send'),
            array('%s', '%s', '%s', '%s', '%s')
            );
    }

    function shortcode_form() {
        ob_start();
        if (locate_template('miklaj-notification-form.php') != '') {
            get_template_part('miklaj-notification-form');
        } else {
            include(M_N_PATH.'/templates/miklaj-notification-form.php');
        }
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

}
