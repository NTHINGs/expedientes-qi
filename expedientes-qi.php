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
if ( file_exists( ABS_DIR . '/shortcode/shortcode-print.php' ) ) {
	require_once( ABS_DIR . '/shortcode/shortcode-print.php' );
}

add_action('wp_enqueue_scripts','expedientes_qi_init');

function expedientes_qi_init() {
    wp_enqueue_script( 'expedientes_qi', plugins_url( '/js/expedientes_qi.js', __FILE__ ));
}