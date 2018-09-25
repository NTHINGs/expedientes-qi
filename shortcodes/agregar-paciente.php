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
	function agregar_paciente_shortcode($atts) {
        $current_user = wp_get_current_user();
        ob_start();
        ?>
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <h1>Estoy en el shortcode :)</h1>
                </div>
                <div class="col-6">
                    <h2>Y con Bootstrap</h2>
                </div>
                <div class="col-12">
                    Usuario loggeado: <?php $current_user->user_login; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
	}
}
