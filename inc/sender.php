<?php

class Miklaj_Notification_Sender{

	function __construct() {
		$blog_name = get_bloginfo('name', 'raw');
		$this->subject = sprintf(__('News from %s', 'miklaj-notification'), $blog_name);
		$this->post_type = array('watchdog-article', 'watchdog-news' );
		$this->composer = new Miklaj_Notification_Composer();
	}

	function get_pending($limit = 0){

		global $wpdb;
		$where = $this->build_where_pending();
		$query = "SELECT * FROM {$wpdb->prefix}miklaj_notification WHERE {$where}";
		if($limit > 0){
			$query .= " LIMIT ${limit}";
		}
		return $wpdb->get_results($query, 'ARRAY_A');

	}
	function build_where_pending(){
		global $wpdb, $mik_laj_notification;
		$frequencies = $mik_laj_notification->frequencies;
		$where = '1 = 0';
		$current_time = current_time('mysql');
		foreach ($frequencies as $key => $options) {
			$time = $options['day'];
			$where .= $wpdb->prepare(
				" OR ( (last_send + INTERVAL %d DAY) < %s AND frequency = %s)", 
				$time,
				$current_time,
				$key
				);
		};
		return $where;
	}

	function send_pending($limit = 0){
		$pending = $this->get_pending($limit);
		if(empty($pending)){
			return;
		}
		foreach ($pending as $notification) {
			$this->send_notification($notification);
		}
		$pending_ids = array_column($pending, 'ID');
		$this->mark_as_sended($pending_ids);
	}

	function send_notification($notification){
		$taxonomies = unserialize($notification['taxonomy']);
		$query = array(
			'post_type' => $this->post_type,
			'tax_query' => array('relation' => 'OR'),
			'posts_per_page' => '10',
			'orderby' => 'post_date',
			'order' => 'DESC',
			'suppress_filters' => true,
			'date_query' => array('after' => $notification['last_send'])
		);

		foreach($taxonomies as $term_type => $term_ids ){
			$query['tax_query'][] = array(
				'taxonomy' => $term_type,
				'field'    => 'term_id',
				'terms'    => $term_ids,
				'operator' => 'IN'
				);
		}
		$posts = get_posts($query);
		if(!empty($posts)){
			$this->send_mail($notification['email'], $posts);
		}
	}


	function send_mail($to, $posts){
		$message = $this->composer->compose_message($posts);
		wp_mail( 
			$to, 
			$this->subject, 
			$message);
	}

	function mark_as_sended($ids){
		global $wpdb;
		$last_send = current_time('mysql');

		$placeholders = array_fill(0, count($ids), '%d');
		$where_format = implode(', ', $placeholders);

		$prepar_func_args = array();
		$prepar_func_args[] = "UPDATE {$wpdb->prefix}miklaj_notification SET `last_send`='%s' WHERE ID IN ({$where_format})";
		$prepar_func_args[] = $last_send;
		$prepar_func_args = array_merge($prepar_func_args, $ids); 
		$prepared_statement = call_user_func_array(array($wpdb, 'prepare'), $prepar_func_args);
		$wpdb->query($prepared_statement);
	}
}