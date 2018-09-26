<?php
/**
 * Mostrar formulario para agregar paciente a la base de datos
 *
 * [agregar-paciente]
 *
 * @package	 expedientes-qi
 * @since    1.0.0
 */

if ( ! function_exists( 'agregar_paciente_shortcode' ) ) {
	// Add the action.
	add_action( 'plugins_loaded', function() {
		// Add the shortcode.
		add_shortcode( 'agregar-paciente', 'agregar_paciente_shortcode' );
	});

	/**
	 * imprimir-expediente shortcode.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	function agregar_paciente_shortcode() {
        $current_user = wp_get_current_user();
        return str_replace("%CURRENT_USER%", $current_user->user_login, file_get_contents( plugin_dir_path(__FILE__) . "/templates/agregar-paciente.html" ));
	}
}
