<?php
/*
Plugin Name: Product list management
Plugin URI: https://abolfazsamiei.ir
Description: مدیریت لیست محصولات ووکامرس [product_list_management_page_shortcode]
Version: 1.0
Author: ابوالفضل سمیعی
Author URI: https://abolfazlsamiei.ir
License: MIT
Text Domain: none
*/
global $wpdb;

//------------- تعریف منو در پنل مدیریت
function Admin_product_list_management_in_wpAdmin()
{
    add_menu_page(
        'گزارش تغییر وضعیت محصولات', // Title of the page
        'لیست فروش', // Text to show on the menu link
        'manage_options',
        'product_list_logs_menu',
        'product_updated_list_wpAdmin',
        'dashicons-format-aside',
        3
    );
    add_submenu_page(
        'product_list_logs_menu',
        'تنظیمات برگه لیست فروش',
        '- تنظیمات',
        'administrator',
        'product_list_logs',
        'product_updated_setting_wpAdmin'
    );

}
add_action( 'admin_menu', 'Admin_product_list_management_in_wpAdmin' );

function product_updated_list_wpAdmin() {
	include 'admin/index.php';
}
function product_updated_setting_wpAdmin() {
    include 'admin/setting.php';
}

function create_productListSetting_db() {
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
	$table_name = $wpdb->prefix . "product_updated_list_settings";

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
	{
        $sql = "CREATE TABLE " . $table_name . " (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		inventory VARCHAR(100) ,
		color VARCHAR(100) ,
		date TIMESTAMP
		)
		CHARACTER SET utf8,
		COLLATE utf8_persian_ci";
        dbDelta( $sql );
	}
}
register_activation_hook( __FILE__, 'create_productListSetting_db' );

function create_productListPermissionsSetting_db() {
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    global $wpdb;
    $table_name = $wpdb->prefix . "product_permission_list_settings";

    if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
    {
        $sql = "CREATE TABLE " . $table_name . " (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		userRoleId VARCHAR(100) ,
		canPriceUpdate VARCHAR(100) ,
		date TIMESTAMP
		)
		CHARACTER SET utf8,
		COLLATE utf8_persian_ci";
        dbDelta( $sql );
    }
}
register_activation_hook( __FILE__, 'create_productListPermissionsSetting_db' );


function create_product_updated_list_log_db() {
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
	$table_name = $wpdb->prefix . "product_logs_list_settings";

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
	{
        $sql = "CREATE TABLE " . $table_name . " (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		productId VARCHAR(100) ,
		userId VARCHAR(100) ,
		price VARCHAR(100) ,
		inventory VARCHAR(100) ,
		updateDate VARCHAR(100) ,
		date TIMESTAMP
		)
		CHARACTER SET utf8,
		COLLATE utf8_persian_ci";
        dbDelta( $sql );
	}
}
register_activation_hook( __FILE__, 'create_product_updated_list_log_db' );
function create_product_target_list_log_db() {
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	global $wpdb;
	$table_name = $wpdb->prefix . "product_target_list_log";

	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name)
	{
        $sql = "CREATE TABLE " . $table_name . " (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		productId VARCHAR(100) ,
		userId VARCHAR(100) ,
		color VARCHAR(100) ,
		date TIMESTAMP
		)
		CHARACTER SET utf8,
		COLLATE utf8_persian_ci";
        dbDelta( $sql );
	}
}
register_activation_hook( __FILE__, 'create_product_target_list_log_db' );



function product_list_management_page_function($atts = array(), $content = null, $tag = ''){

//    print_r( admin_url( 'admin-ajax.php' ));
//print_r($atts);
//print_r($content);
//print_r($tag);
	print_r(wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) ));
//	wp_register_script( 'ajax-script', plugin_dir_url( __FILE__ ) . '/src/jav.js', array( 'jquery' ), 1.0 );
	wp_enqueue_script( 'ajax-script' );
	wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

    include WP_PLUGIN_DIR."/product-list-management/public/index.php";
}

add_shortcode('product_list_management_page_shortcode', 'product_list_management_page_function');




