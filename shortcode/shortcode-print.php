<?php
/**
 * Imprimir expediente de paciente
 *
 * [imprimir-expediente]
 *
 * @package	 expedientes-qi
 * @since    1.0.0
 */

if ( ! function_exists( 'imprimir_expediente_shortcode' ) ) {
	// Add the action.
	add_action( 'plugins_loaded', function() {
		// Add the shortcode.
		add_shortcode( 'imprimir-expediente', 'imprimir_expediente_shortcode' );
	});

	/**
	 * imprimir-expediente shortcode.
	 *
     * @param  Attributes $atts
	 * @return string
	 * @since  1.0.0
	 */
	function imprimir_expediente_shortcode($atts) {
        $_atts = shortcode_atts( array(
			'mode'  => 'default',
        ), $atts );
        
        return '<button class="btn btn-primary pull-right" onclick="expedientes_qi_imprimir(\''. $_atts['mode'] . '\')">Imprimir</button>';
	}
}
