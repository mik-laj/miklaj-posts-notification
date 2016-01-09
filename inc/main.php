<?php

class Miklaj_Notification_Main{

	function __construct() {
		// Do nothing
	}
	function initialize(){
		$this->load_text_domain();
		// add_action('plugins_loaded', array($this, 'load_text_domain'));
		$this->frequencies = array(
			0 => array(
				'label' => __('Everyday', 'miklaj-notification'),
				'day' => 1
				),
			1 => array(
				'label' => __('Every week', 'miklaj-notification'),
				'day' => 7
				),
			2 => array(
				'label' => __('Every month', 'miklaj-notification'),
				'day' => 30
				),
			);
		$this->shortcode = new Miklaj_Notification_Shortcode();
		$this->admin = new Miklaj_Notification_Admin();
		$this->sender = new Miklaj_Notification_Sender();
		$this->cron = new Miklaj_Notification_Cron();
	}
	function load_text_domain(){
		$plugin_dir = basename(M_N_PATH).'/languages/';
		load_plugin_textdomain( 'miklaj-notification', false, $plugin_dir );
	}
}