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
        $table_responsables = $wpdb->prefix . "expedientes_responsables";
        $current_user = wp_get_current_user();

        $isAdmin = current_user_can('expedientes') && current_user_can('expedientes_admin');
        $sql = "SELECT $table_pacientes.* FROM $table_pacientes, $table_responsables WHERE $table_responsables.paciente=$table_pacientes.id AND $table_responsables.responsable = '{$current_user->user_login}'";
        if ($isAdmin == true) {
            $sql = "SELECT * FROM $table_pacientes";
        } 
        $pacientes = $wpdb->get_results(
            $sql,
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