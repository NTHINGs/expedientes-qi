<?php
/**
 * Plugin Name:       Expedientes QI
 * Plugin URI:        https://github.com/NTHINGs/expedientes-qi
 * Description:       Coding shortcodes in a plugin with maintainable code practices.
 * Version:           1.0.0
 * Author:            Mauricio Martinez
 * Author URI:        https://github.com/NTHINGs
 * License:           MIT
 * License URI:       https://github.com/NTHINGs/expedientes-qi/blob/master/LICENSE
 * Text Domain:       expedientes-qi
 *
 * @link              https://github.com/NTHINGs/expedientes-qi
 * @package           expedientes-qi
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define global constants.
 *
 * @since 1.0.0
 */
// Plugin version.
if ( ! defined( 'ABS_VERSION' ) ) {
	define( 'ABS_VERSION', '1.0.0' );
}

if ( ! defined( 'ABS_NAME' ) ) {
	define( 'ABS_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
}

if ( ! defined( 'ABS_DIR' ) ) {
	define( 'ABS_DIR', WP_PLUGIN_DIR . '/' . ABS_NAME );
}

if ( ! defined( 'ABS_URL' ) ) {
	define( 'ABS_URL', WP_PLUGIN_URL . '/' . ABS_NAME );
}

/**
 * Link.
 *
 * @since 1.0.0
 */
if ( file_exists( ABS_DIR . '/shortcodes/shortcode-print.php' ) ) {
	require_once( ABS_DIR . '/shortcodes/shortcode-print.php' );
}
if ( file_exists( ABS_DIR . '/shortcodes/agregar-paciente.php' ) ) {
	require_once( ABS_DIR . '/shortcodes/agregar-paciente.php' );
}

add_action('wp_enqueue_scripts','expedientes_qi_init');

function expedientes_qi_init() {
	wp_enqueue_script( 'bootstrap', plugins_url('/js/bootstrap.min.js', __FILE__ ))
    wp_enqueue_script( 'jspdf', plugins_url( '/js/jspdf.min.js', __FILE__ ));
	wp_enqueue_script( 'expedientes_qi', plugins_url( '/js/expedientes_qi.js', __FILE__ ));
	wp_enqueue_style( 'bootstrapcss', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');
}

// Create Tables
register_activation_hook( __FILE__, 'create_plugin_database' );
function create_plugin_database() {
    global $table_prefix, $wpdb;

	$sql = str_replace("%TABLE_PREFIX%", $table_prefix, file_get_contents( plugin_dir_path(__FILE__) . "/schema.sql" ));
	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}
