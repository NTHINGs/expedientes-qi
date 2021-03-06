<?php
/**
 * Plugin Name:       Expedientes QI
 * Plugin URI:        https://github.com/NTHINGs/expedientes-qi
 * Description:       Coding shortcodes in a plugin with maintainable code practices.
 * Version:           1.0.1
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

/**
 * Link.
 *
 * @since 1.0.0
 */
if ( file_exists( ABS_DIR . '/shortcodes/services.php' ) ) {
	require_once( ABS_DIR . '/shortcodes/services.php' );
}
if ( file_exists( ABS_DIR . '/shortcodes/paciente.php' ) ) {
	require_once( ABS_DIR . '/shortcodes/paciente.php' );
}
if ( file_exists( ABS_DIR . '/shortcodes/pacientes.php' ) ) {
	require_once( ABS_DIR . '/shortcodes/pacientes.php' );
}

add_action('wp_enqueue_scripts','expedientes_qi_init');

function expedientes_qi_init() {
	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', array(), null, true);
	wp_register_script( 'popper', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array( 'jquery' ), '3.3.1', false );
	wp_enqueue_script( 'bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js', array( 'jquery', 'popper' ), '3.3.1', false );
	wp_enqueue_script( 'gijgo', '//cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js', array( 'jquery' ), '3.3.1', false );
	wp_enqueue_script( 'autocomplete', plugins_url( '/js/autocomplete.js', __FILE__ ));
	wp_enqueue_style( 'autocomplete', plugins_url( '/css/autocomplete.css', __FILE__ ));
	wp_enqueue_style( 'gijgo', '//cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css');
	wp_enqueue_style( 'rangeslider', plugins_url( '/css/slider.css', __FILE__));
    wp_enqueue_script( 'jspdf', plugins_url( '/js/jspdf.min.js', __FILE__ ));
    wp_enqueue_script( 'jspdf-tables', plugins_url( '/js/jspdf.plugin.autotable.js', __FILE__ ));
	wp_enqueue_script( 'expedientes_qi', plugins_url( '/js/expedientes_qi.js', __FILE__ ));
}

// Create Tables
function create_plugin_database() {
    global $table_prefix, $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$sql = str_replace(array("%TABLE_PREFIX%", "%CHARSET_COLLATE%"), array($table_prefix . "expedientes_", $charset_collate), file_get_contents( plugin_dir_path(__FILE__) . "/schema.sql" ));
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);

	add_role( 'clinica', 'Clínica', 
		array( 
			'expedientes' => true
		)
	);
	add_role( 'director-clinica', 'Director Clínica', 
		array( 
			'expedientes_admin'=> true
		)
	);
}
register_activation_hook( __FILE__, 'create_plugin_database' );
