<?php
class Miklaj_Notification_Composer{
	function __construct() {
		$this->ensure_wp_rewrite_is_loaded();
	}
	function compose_message($posts){

		$output = '';
		foreach ($posts as $post) {
			$title = get_the_title($post);
			$permalink = get_permalink($post);
			$output .= sprintf("%s \n%s\n\n", $title, $permalink);
		}
		return $output;
	}

	function ensure_wp_rewrite_is_loaded(){
		global $wp_rewrite;

		if ( ! isset( $wp_rewrite ) ) {
			require_once ABSPATH . WPINC . '/rewrite.php';
			$wp_rewrite = new WP_Rewrite();
		}
	}
}