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
		// Get mode
        $_atts = shortcode_atts( array(
			'mode'  => 'default',
			'paciente_id' => null,
		), $atts );

		// Query for patient
		global $wpdb;
		$table_pacientes = $wpdb->prefix . "expedientes_pacientes";
		$table_contactos = $wpdb->prefix . "expedientes_personas_contacto";
		$table_riesgos = $wpdb->prefix . "expedientes_riesgos_psicosociales";
		$table_sustancias = $wpdb->prefix . "expedientes_psicotropicos";
		$table_name_esquema_fases = $wpdb->prefix . "expedientes_esquema_fases";
		$table_name_fad = $wpdb->prefix . "expedientes_fad";
		$table_notas_progreso = $wpdb->prefix . "expedientes_notas_progreso";
		$table_archivos_adjuntos = $wpdb->prefix . "expedientes_archivos_adjuntos";

		$result = $wpdb->get_results("SELECT * FROM $table_pacientes WHERE id = {$_atts['paciente_id']}", 'ARRAY_A');
		
		// Escape quotes from json
		$patient = base64_encode(json_encode($result));

        return '<button class="btn btn-primary pull-right" onclick="expedientes_qi_imprimir(\''. $_atts['mode'] . '\', \'' . $patient . '\')">Imprimir</button>';
	}
}
