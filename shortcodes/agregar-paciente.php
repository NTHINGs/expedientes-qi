<?php
/**
 * Mostrar formulario para agregar paciente a la base de datos
 *
 * [agregar-paciente]
 *
 * @package	 expedientes-qi
 * @since    1.0.0
 */

global $wpdb;

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
        if ( !function_exists( 'wp_handle_upload' ) ){
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
    
        if ( isset( $_POST['submitted'] ) ) {
            $fotografia = '/wp-content/plugins/expedientes-qi/default.png';
            if ($_FILES['fotografia']['size'] > 0 && $_FILES['fotografia']['error'] == 0) {
                // Se subio una foto
                $upload_overrides = array( 'test_form' => false );
                $movefile = wp_handle_upload( $_FILES['fotografia'], $upload_overrides );
                if ( $movefile && ! isset( $movefile['error'] ) ) {
                    // echo "File is valid, and was successfully uploaded.\n" . $movefile['url'];
                    // var_dump( $movefile );
                    $fotografia = preg_replace('#^https?://#', '', rtrim($movefile['url'], '/'));
                } else {
                    /**
                     * Error generated by _wp_handle_upload()
                     * @see _wp_handle_upload() in wp-admin/includes/file.php
                     */
                    echo $movefile['error'];
                }
            } else {
                // No se subio una foto
            }

            $table_name = $wpdb->prefix . "expedientes_paciente";
            $wpdb->insert( $table_name, array(
                'fotografia' => $fotografia,
                'nombre'            => $_POST['nombre'],
                'fechadenacimiento' => date($_POST['fechadenacimiento']),
                'edad'              => $_POST['edad'],
                'escolaridad'       => $_POST['escolaridad'],
                'ocupacion'         => $_POST['ocupacion'],
                'estadocivil'       => $_POST['estadocivil'],
                'cantidadhijos'     => $_POST['cantidadhijos'],
                'domicilio'         => $_POST['domicilio'],
                'ciudaddeorigen'    => $_POST['ciudaddeorigen'],
                'telefono'          => $_POST['telefono'],
                'email'             => $_POST['email'],
                'enfermedades'      => $_POST['enfermedades'],
                'alergias'          => $_POST['alergias'],
                'responsable'       => $_POST['responsable']
            ));
            
            echo $_POST['nombre'] . ' agregado correctamente';
        }
    }
}
