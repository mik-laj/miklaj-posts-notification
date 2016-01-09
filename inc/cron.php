<?php
class Miklaj_Notification_Cron{
    function __construct() {
        add_action('miklaj_notification_hourly_event', array($this, 'hourly_event'));

        if ( !wp_next_scheduled('miklaj_notification_hourly_event') )
            wp_schedule_event(time(), 'hourly', 'miklaj_notification_hourly_event');
        // $this->hourly_event();
    }

    function hourly_event(){
        // $file = '/tmp/wp/test.txt';
        // $person = date('l jS F Y h:i:s A')."\n";
        // file_put_contents(dirname(__FILE__).'/aaa.txt', $person, FILE_APPEND );
        $GLOBALS['mik_laj_notification']->sender->send_pending();
    }

}