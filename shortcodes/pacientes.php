<?php
/**
 * Mostrar lista pacientes
 *
 * [mostrar-pacientes]
 *
 * @package	 expedientes-qi
 * @since    1.0.1
 */
if ( ! function_exists( 'mostrar_pacientes_shortcode' ) ) {
	// Add the action.
	add_action( 'plugins_loaded', function() {
		// Add the shortcode.
		add_shortcode( 'mostrar-pacientes', 'mostrar_pacientes_shortcode' );
	});

	/**
	 * mostrar-pacientes shortcode.
	 *
	 * @return string
	 * @since  1.0.1
	 */
	function mostrar_pacientes_shortcode() {
        ob_start();
        mostrar_pacientes_render_html();
        return ob_get_clean();
    }

    function mostrar_pacientes_render_html() {
        global $wpdb;
        $table_pacientes = $wpdb->prefix . "expedientes_pacientes";
        $current_user = wp_get_current_user();
        $pacientes = $wpdb->get_results(
            "SELECT * FROM $table_pacientes WHERE responsable = '{$current_user->display_name}'", 
            'ARRAY_A'
        );
        $variables = array(
            '%PACIENTES%',
        );
        $values = array(
            json_encode($pacientes), 
        );
        echo str_replace($variables, $values, file_get_contents(  plugin_dir_path( __DIR__ ) . "templates/pacientes.html" ));
    }

}