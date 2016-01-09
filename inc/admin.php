<?php
class Miklaj_Notification_Admin {

    public $notification_list_obj;

    // class constructor
    public function __construct() {
        $this->register_listener();
    }
    
    function register_listener() {
        add_filter( 'set-screen-option', [ $this, 'set_screen' ], 10, 3 );
        add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
    }

    public function set_screen( $status, $option, $value ) {
        return $value;
    }

    public function plugin_menu() {

        $hook = add_menu_page(
            __('Notification list', 'miklaj-notification'),
            __('Notification', 'miklaj-notification'),
            'manage_options',
            'watchdogportal_notification_list',
            [ $this, 'plugin_settings_page' ],
            'dashicons-email'
            );

        add_action( "load-$hook", [ $this, 'screen_option' ] );

    }

    public function screen_option() {

        $option = 'per_page';
        $args   = [
            'label'   => __('Address', 'miklaj-notification'),
            'default' => 5,
            'option'  => 'addressess_per_page'
        ];

        add_screen_option( $option, $args );

        $this->notification_list_obj = new Miklaj_Notification_List();
    }
    public function plugin_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Notification list', 'miklaj-notification'); ?> </h1>

            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php
                                $this->notification_list_obj->prepare_items();
                                $this->notification_list_obj->display(); ?>
                            </form>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
        <?php
    }

}
?>