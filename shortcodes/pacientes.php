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
    
    add_action( 'wp_ajax_nopriv_expedientes_eliminar_paciente', 'expedientes_eliminar_paciente' );
    add_action( 'wp_ajax_expedientes_eliminar_paciente', 'expedientes_eliminar_paciente' );

    function expedientes_eliminar_paciente() {
        global $wpdb;
        $id = $_POST['id'];
        $table_pacientes = $wpdb->prefix . "expedientes_pacientes";
        $wpdb->delete(
            $table_pacientes,
            [ 'id' => $id ],
            [ '%d' ]
        );
        echo 'ok';
        die();
    }

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
            '%AJAX_URL%'
        );
        $values = array(
            json_encode($pacientes),
            admin_url( 'admin-ajax.php' )
        );
        echo str_replace($variables, $values, file_get_contents(  plugin_dir_path( __DIR__ ) . "templates/pacientes.html" ));
    }

}