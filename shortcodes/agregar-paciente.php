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
        ob_start();
        guardar_paciente();
        render_html();
        return ob_get_clean();
    }
    
    function render_html() {
        $current_user = wp_get_current_user();
        $variables = array("%CURRENT_USER%", "%REQUEST_URI%");
        $values = array($current_user->user_login, esc_url( $_SERVER['REQUEST_URI'] ));
        echo str_replace($variables, $values, file_get_contents( plugin_dir_path( __DIR__ ) . "/templates/agregar-paciente.html" ));
    }

    function guardar_paciente() {
        if ( isset( $_POST['submitted'] ) ) {
            echo 'NOMBRE RECIBIDO: ' . $_POST['nombre'];
        }
    }
}
