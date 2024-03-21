<?php
/*
Plugin Name: noyan crm auth
Plugin URI: https://abolfazsamiei.ir
Description: مدیریت دسترسی به برنامه سی ار ام [noyan_crm_management_shortcode]
Version: 1.0
Author: ابوالفضل سمیعی
Author URI: https://abolfazlsamiei.ir
License: MIT
Text Domain: none
*/
global $wpdb;

//------------- تعریف منو در پنل مدیریت
function Admin_noyan_crm_auth_management_in_wpAdmin()
{
    add_menu_page(
        'تنظیمات دسترسی CRM', // Title of the page
        'تنظیمات CRM', // Text to show on the menu link
        'manage_options',
        'crm_auth_setting_menu',
        'crm_auth_wpAdmin',
        'dashicons-format-aside',
        3
    );
//    add_submenu_page(
//        'product_list_logs_menu',
//        'تنظیمات',
//        '- تنظیمات',
//        'administrator',
//        'product_list_logs',
//        'product_updated_setting_wpAdmin'
//    );

}
add_action( 'admin_menu', 'Admin_noyan_crm_auth_management_in_wpAdmin' );

function crm_auth_wpAdmin() {
	include 'admin/index.php';
}
//function product_updated_setting_wpAdmin() {
//    include 'admin/setting.php';
//}

function create_noyan_crm_Setting_db() {
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
	$table_name = $wpdb->prefix . "noyan_crm_settings";

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
	{
        $sql = "CREATE TABLE " . $table_name . " (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		client_id VARCHAR(100) ,
		resource VARCHAR(100) ,
		username VARCHAR(100) ,
		code VARCHAR(100) ,
		client_secret VARCHAR(100) ,
		grant_type VARCHAR(100) ,
		exp VARCHAR(100) ,
		access_token longtext ,
		token_type longtext ,
		date TIMESTAMP
		)
		CHARACTER SET utf8,
		COLLATE utf8_persian_ci";
        dbDelta( $sql );
	}
}
register_activation_hook( __FILE__, 'create_noyan_crm_Setting_db' );


add_shortcode('product_list_management_page_shortcode', 'noyan_crm_management_shortcode');




