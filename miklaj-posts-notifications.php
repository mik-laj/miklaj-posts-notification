<?php
/*
Plugin Name:  Mik-laj's Posts notifications
Description:  Allow to send notification to subscribed user.
Version:      0.0.1
Author:       Kamil Bregula (mik-laj)
Author URI:   http://www.pilnujemy.info
Text Domain:  miklaj-notification
*/

define('M_N_PATH', plugin_dir_path(__FILE__));
define('M_N_FILE', __FILE__);

require_once(M_N_PATH . '/inc/cron.php');
require_once(M_N_PATH . '/inc/shortcode.php');
require_once(M_N_PATH . '/inc/list.php');
require_once(M_N_PATH . '/inc/composer.php');
require_once(M_N_PATH . '/inc/sender.php');
require_once(M_N_PATH . '/inc/admin.php');
require_once(M_N_PATH . '/inc/main.php');

global $mik_laj_notification;
$mik_laj_notification = new Miklaj_Notification_Main();

add_action('plugins_loaded', array($mik_laj_notification, 'initialize'));
// $GLOBALS['mik_laj_notification']->initialize();


