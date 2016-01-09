<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Miklaj_Notification_List extends WP_List_Table {

    /** Class constructor */
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'Address', 'miklaj-notification' ),
            'plural'   => __( 'Addresses', 'miklaj-notification' ),
            'ajax'     => false
            ]
            );

    }

    public static function get_addresses( $per_page = 5, $page_number = 1 ) {

        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}miklaj_notification";

        if ( ! empty( $_REQUEST['orderby'] ) ) {
            $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
            $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }

        $sql .= " LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;


        $result = $wpdb->get_results( $sql, 'ARRAY_A' );

        return $result;
    }

    public static function delete_address( $id ) {
        global $wpdb;

        $wpdb->delete(
            "{$wpdb->prefix}miklaj_notification",
            array( 'ID' => $id ),
            array( '%d' )
            );
    }

    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}miklaj_notification";

        return $wpdb->get_var( $sql );
    }

    public function no_items() {
        _e( 'No address.', 'miklaj-notification' );
    }

    function column_name( $item ) {

        $delete_nonce = wp_create_nonce( 'miklaj-notification_delete_address' );

        $title = '<strong>' . $item['name'] . '</strong>';

        $actions = [
        'delete' => sprintf(
            '<a href="?page=%s&action=%s&address=%s&_wpnonce=%s">%s</a>',
            esc_attr( $_REQUEST['page'] ),
            'delete',
            absint( $item['ID'] ),
            $delete_nonce,
            __('Delete')
            )
        ];

        return $title . $this->row_actions( $actions );
    }

    function get_columns() {
        $columns = [
        'cb'            => '<input type="checkbox" />',
        'name'          => __( 'Name', 'miklaj-notification' ),
        'email'         => __( 'Email', 'miklaj-notification' ),
        'frequency'     => __( 'Notification frequency', 'miklaj-notification' ),
        'last_send'     => __( 'Last send', 'miklaj-notification' ),
        'taxonomy'      => __( 'Taxonomy', 'miklaj-notification' ),
        ];
        return $columns;
    }

    public function get_sortable_columns() {
        $sortable_columns = array(
            'name' => array( 'name', true ),
            'email' => array( 'email', false ),
            'last_send' => array( 'last_send', false )
            );

        return $sortable_columns;
    }
    public function column_default( $item, $column_name ) {
        return print_r( $item, true );
    }

    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']
            );
    }

    function column_email( $item ) {
        return $item['email'];
    }

    function column_frequency( $item ){
        $frequencies = $GLOBALS['mik_laj_notification']->frequencies;
        return $frequencies[$item['frequency']]['label'];
    }

    function column_taxonomy( $item ){
        $output = '';
        $taxonomies = unserialize($item['taxonomy']);
        foreach($taxonomies as $tax => $term_ids){
            $output .= get_taxonomy($tax)->label.': ';
            $term_list = array();
            foreach ($term_ids as $term_id) {
                $term_list[] = get_term($term_id, $tax)->name;
            }
            $output.= implode(', ', $term_list).'; ';
        }
        return $output;
    }

    function column_last_send( $item ){
        $m_time = $item['last_send'];
        $time = mysql2date('U', $m_time, false);
        if ( ( abs( $t_diff = time() - $time ) ) < DAY_IN_SECONDS ) {
            if ( $t_diff < 0 ) {
                $h_time = sprintf( __( '%s from now' ), human_time_diff( $time ) );
            } else {
                $h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
            }
        } else {
            $h_time = mysql2date( __( 'Y/m/d' ), $m_time );
        }

        echo $h_time;
    }


    public function get_bulk_actions() {
        $actions = [
        'bulk-delete' => __('Delete')
        ];

        return $actions;
    }
    public function prepare_items() {

        $this->_column_headers = $this->get_column_info();

        /** Process bulk action */
        $this->process_bulk_action();

        $per_page     = $this->get_items_per_page( 'addressess_per_page', 5 );
        $current_page = $this->get_pagenum();
        $total_items  = self::record_count();

        $this->set_pagination_args( [
            'total_items' => $total_items,
            'per_page'    => $per_page
            ] );


        $this->items = self::get_addresses( $per_page, $current_page );
    }

    public function process_bulk_action() {


        if ( 'delete' === $this->current_action() ) {
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );
            if ( ! wp_verify_nonce( $nonce, 'miklaj-notification_delete_address' ) ) {
                die( );
            } else {
                self::delete_address( $_GET['address'] );

                wp_redirect( esc_url( add_query_arg() ) );
                exit;
            }
        }

        if (( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
            || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
            )
        {

            foreach ( $delete_ids as $id ) {
                self::delete_address( $id );
            }

            wp_redirect( esc_url( add_query_arg() ) );
            exit;
        }
    }
}
