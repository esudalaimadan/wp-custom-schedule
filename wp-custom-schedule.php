<?php
/*
  Plugin Name: Adds Custom Schedules
  Description: Enables you to add your own custom schedules
*/
class CronSchedule{
    /* Constructor for class */
    public function __construct(){
        add_action('admin_menu', array($this, 'add_plugin_page') );
        add_action('admin_init', array($this, 'page_init') );
        add_filter('cron_schedules', array( $this, 'filter_cron_schedules' ) );
        register_activation_hook( __FILE__, array( $this, 'plugin_activate' ) );
        register_deactivation_hook(__FILE__, array($this, 'plugin_deactivate'));
    }

    /*Adds a submenu 'Cron Schedules' under Tools menu */
    public function add_plugin_page(){
        add_management_page('Cron Schedules', 'Cron Schedules', 'manage_options', 'cron-schedule', array($this, 'create_page'));
    }

    /* 'Cron Schedules' page callback */
    public function create_page(){
        /* TODO display available schedules in a table
        print_r(wp_get_schedules());
        */
        ?>
        <div class="wrap">
            <h1>Cron Schedules</h1>
            <?php settings_errors(); ?>
            <form method="post" action="options.php">
            <?php 
                //Prints out all hidden fields
                settings_fields('custom-schedules-group');
                do_settings_sections('custom-schedules-admin');
                submit_button();
            ?>
            </form>
        </div>
        <?php    
    }

    // Adds necessary fields
    public function page_init(){
        register_setting('custom-schedules-group', 'custom_schedules', array($this, 'sanitize'));
        add_settings_section('cust-secid','Custom Schedules-1', array($this, 'print_section_info'), 'custom-schedules-admin');
        add_settings_field('internal-name', 'Internal Name', array($this, 'print_internal_name'),'custom-schedules-admin', 'cust-secid' );
        add_settings_field('interval', 'Interval(In Seconds)', array($this, 'print_interval'),'custom-schedules-admin', 'cust-secid' );
        add_settings_field('display-name', 'Display Name', array($this, 'print_display_name'),'custom-schedules-admin', 'cust-secid' );
    }
    
    //Sanitizes input and stores custom schedules in wp_options
    public function sanitize($input){
        $new_input = array();
        if(isset($input['internal_name'])){
            $internal_name = sanitize_text_field($input['internal_name']);            
        }
        if(isset($input['interval'])){
            $interval = absint($input['interval']);            
        }
        if(isset($input['display_name'])){
            $display = sanitize_text_field($input['display_name']);            
        }
        $new_input[$internal_name] = array('interval' => $interval, 'display' => $display);
        $db_schedules = get_option('custom_schedules');
        return array_merge($db_schedules, $new_input);
    }

    public function print_section_info(){
        print "Enter your custom schedules below";
    }

    public function print_internal_name(){
        printf('<input type="text" id="internal-name" name="custom_schedules[internal_name]" />');
    }

    public function print_interval(){
        printf('<input type="text" id="interval" name="custom_schedules[interval]" />');
    }

    public function print_display_name(){
        printf('<input type="text" id="display-name" name="custom_schedules[display_name]" />');
    }

    public function filter_cron_schedules($schedules){
        $db_schedules = get_option('custom_schedules', array());
        return array_merge($schedules, $db_schedules);    
    }

    public function plugin_activate(){
        add_option('custom_schedules', array());
    }
    
    public function plugin_deactivate(){
        unregister_setting('custom-schedules-group', 'custom_schedules');
        delete_option('custom_schedules');
    }
}

if(is_admin()){
    $cron_schedule = new CronSchedule();
}   

?>