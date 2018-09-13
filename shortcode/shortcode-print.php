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

        $js = "
        function imprimir(mode) {
            console.log(mode);
            <?php do_action('convertir_pdf', '`. $_atts['mode'] . `');?>
        }
        ";
        return do_shortcode("[js]" . $js . "[/js]") . `
            <button class='btn btn-primary pull-right' onclick="imprimir('`. $_atts['mode'] . `')">Imprimir</button>
        `;
	}
}
