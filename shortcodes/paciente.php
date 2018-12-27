<?php
session_start();
/**
 * Mostrar formulario para agregar paciente a la base de datos
 *
 * [paciente]
 *
 * @package	 expedientes-qi
 * @since    1.0.0
 */
if ( ! function_exists( 'paciente_shortcode' ) ) {
	// Add the action.
	add_action( 'plugins_loaded', function() {
		// Add the shortcode.
		add_shortcode( 'paciente', 'paciente_shortcode' );
    });
    
	/**
	 * imprimir-expediente shortcode.
	 *
	 * @return string
	 * @since  1.0.0
	 */
	function paciente_shortcode() { 
        ob_start();
        $paciente_id = null;
        if (isset($_GET['paciente'])) {
            $paciente_id = $_GET['paciente'];
        }
        $permiso = validar_permisos($paciente_id);
        if ($permiso == true) {
            if ( isset( $_POST['submitted'] ) && $_SESSION['rand'] != $_POST['randcheck'] ) {
                guardar_paciente($paciente_id);
            } else {
                render_html($paciente_id);
            }
        } else {
            echo '<h1>NO TIENES ACCESO A ESTE EXPEDIENTE. Comunícate con un encargado para pedir acceso.</h1>';
        }
        return ob_get_clean();
    }

    function validar_permisos($paciente_id) {
        if (!isset($paciente_id)) {
            return true;
        }
        $isAdmin = current_user_can('expedientes') && current_user_can('expedientes_admin');
        if ($isAdmin == true) {
            return true;
        }
        global $wpdb;
        $table_responsables = $wpdb->prefix . "expedientes_responsables";
        $current_user = wp_get_current_user();
        $responsables = $wpdb->get_results(
            "SELECT * FROM $table_responsables WHERE paciente = '{$paciente_id}' AND responsable = '{$current_user->user_login}'",
            'ARRAY_A'
        );

        return count($responsables) > 0;
    }
    
    function render_html($paciente_id) {
        $responsable = null;
        $paciente = null;
        if($paciente_id != null) {
            global $wpdb;
            $table_pacientes = $wpdb->prefix . "expedientes_pacientes";
            $table_contactos = $wpdb->prefix . "expedientes_personas_contacto";
            $table_riesgos = $wpdb->prefix . "expedientes_riesgos_psicosociales";
            $table_sustancias = $wpdb->prefix . "expedientes_psicotropicos";
            $table_name_esquema_fases = $wpdb->prefix . "expedientes_esquema_fases";
            $table_name_fad = $wpdb->prefix . "expedientes_fad";
            $table_notas_progreso = $wpdb->prefix . "expedientes_notas_progreso";
            $table_archivos_adjuntos = $wpdb->prefix . "expedientes_archivos_adjuntos";
            $table_evaluaciones_psicologicas = $wpdb->prefix . "expedientes_evaluaciones_psicologicas";
            $table_responsables = $wpdb->prefix . "expedientes_responsables";

            $paciente = $wpdb->get_results(
                "SELECT * FROM $table_pacientes WHERE id = '{$paciente_id}'", 
                'ARRAY_A'
            )[0];
            if(!empty($paciente)) {
                $paciente['contactos'] = $wpdb->get_results(
                    "SELECT * FROM $table_contactos WHERE paciente = '{$paciente_id}'", 
                    'ARRAY_A'
                );
                $paciente['riesgos'] = $wpdb->get_results(
                    "SELECT * FROM $table_riesgos WHERE paciente = '{$paciente_id}'", 
                    'ARRAY_A'
                )[0];
                $paciente['sustancias'] = $wpdb->get_results(
                    "SELECT * FROM $table_sustancias WHERE paciente = '{$paciente_id}'", 
                    'ARRAY_A'
                );
                $paciente['fases'] = $wpdb->get_results(
                    "SELECT * FROM $table_name_esquema_fases WHERE paciente = '{$paciente_id}'", 
                    'ARRAY_A'
                )[0];
                unset( $paciente['fases']['id']);
                unset( $paciente['fases']['paciente']);
                foreach( $paciente['fases'] as $key => $value) {
                    $paciente['fases'][$key] = json_decode($paciente['fases'][$key]);
                }
                $paciente['fad'] = $wpdb->get_results(
                    "SELECT * FROM $table_name_fad WHERE paciente = '{$paciente_id}'", 
                    'ARRAY_A'
                )[0];
                $paciente['notas_progreso'] = $wpdb->get_results(
                    "SELECT * FROM $table_notas_progreso WHERE paciente = '{$paciente_id}'",
                    'ARRAY_A'
                );
                $paciente['archivos_adjuntos'] = $wpdb->get_results(
                    "SELECT * FROM $table_archivos_adjuntos WHERE paciente = '{$paciente_id}'",
                    'ARRAY_A'
                );
                $paciente['evaluaciones_psicologicas'] = $wpdb->get_results(
                    "SELECT * FROM $table_evaluaciones_psicologicas WHERE paciente = '{$paciente_id}'",
                    'ARRAY_A'
                );
                $paciente['responsables'] = $wpdb->get_results(
                    "SELECT * FROM $table_responsables WHERE paciente = '{$paciente_id}'",
                    'ARRAY_A'
                );
            } else {
                $paciente = array('error' => true);
            }
        }
        $current_user = wp_get_current_user();
        $variables = array("%IS_ADMIN%", "%REQUEST_URI%", "%PACIENTE%", "%RAND%", "%AJAX_URL%", "%CURRENT_USER%");
        $values = array(current_user_can('expedientes_admin'), esc_url( $_SERVER['REQUEST_URI'] ), json_encode($paciente), rand(), admin_url( 'admin-ajax.php' ), $current_user->user_login);
        echo str_replace($variables, $values, file_get_contents( plugin_dir_path( __DIR__ ) . "/templates/paciente.html" ));
    }

    function guardar_paciente($paciente_id) {
        $error = false;
        if ( !function_exists( 'wp_handle_upload' ) ){
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        $_SESSION['rand'] = $_POST['randcheck'];
        global $wpdb;
        $fotografia = '/wp-content/plugins/expedientes-qi/default.png';
        $table_name = $wpdb->prefix . "expedientes_pacientes";
        $table_name_contactos = $wpdb->prefix . "expedientes_personas_contacto";
        $table_name_riesgos = $wpdb->prefix . "expedientes_riesgos_psicosociales";
        $table_name_psicotropicos = $wpdb->prefix . "expedientes_psicotropicos";
        $table_name_esquema_fases = $wpdb->prefix . "expedientes_esquema_fases";
        $table_name_fad = $wpdb->prefix . "expedientes_fad";
        $table_notas_progreso = $wpdb->prefix . "expedientes_notas_progreso";
        $table_archivos_adjuntos = $wpdb->prefix . "expedientes_archivos_adjuntos";
        $table_evaluaciones_psicologicas = $wpdb->prefix . "expedientes_evaluaciones_psicologicas";
        $table_responsables = $wpdb->prefix . "expedientes_responsables";
        // Nuevo Paciente
        if ($_FILES['fotografia']['size'] > 0 && $_FILES['fotografia']['error'] == 0) {
            // Se subio una foto
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $_FILES['fotografia'], $upload_overrides );
            if ( $movefile && ! isset( $movefile['error'] ) ) {
                // echo "File is valid, and was successfully uploaded.\n" . $movefile['url'];
                // var_dump( $movefile );
                $fotografia = parse_url($movefile['url'])['path'];
            } else {
                /**
                 * Error generated by _wp_handle_upload()
                 * @see _wp_handle_upload() in wp-admin/includes/file.php
                 */
                echo $movefile['error'];
                $error = true;
            }
        } else {
            // No se subio una foto
            if ($_POST['editmode'] == '1') {
                $fotografia = $paciente = $wpdb->get_results(
                    "SELECT * FROM $table_name WHERE id = '{$paciente_id}'", 
                    'ARRAY_A'
                )[0]['fotografia'];
            }
        }

        // PACIENTE

        $values = array(
            'fotografia'         => $fotografia,
            'nombre'             => $_POST['nombre'],
            'fechadenacimiento'  => date('Y-m-d', strtotime($_POST['fechadenacimiento'])),
            'edad'               => $_POST['edad'],
            'escolaridad'        => $_POST['escolaridad'],
            'ocupacion'          => $_POST['ocupacion'],
            'estadocivil'        => $_POST['estadocivil'],
            'cantidadhijos'      => $_POST['cantidadhijos'],
            'domicilio'          => $_POST['domicilio'],
            'ciudaddeorigen'     => $_POST['ciudaddeorigen'],
            'ciudadactual'       => $_POST['ciudadactual'],
            'telefono'           => $_POST['telefono'],
            'email'              => $_POST['email'],
            'enfermedades'       => $_POST['enfermedades'],
            'alergias'           => $_POST['alergias'],
            'fecha_creacion'     => current_time( 'mysql' ),
            'fecha_modificacion' => current_time( 'mysql' )
        );

        if ($_POST['editmode'] != '1') {
            $wpdb->insert( $table_name, $values, array(
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            ));
            $paciente_id = $wpdb->insert_id;
        } else {
            $wpdb->update( $table_name, $values, array('id' => $paciente_id ), array(
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            ), '%d');
        }


        // CONTACTOS
        if (!empty($_POST['contactos_delete'])) {
            foreach( explode(",", $_POST['contactos_delete']) as $key => $id) {
                $wpdb->delete(
                    $table_name_contactos,
                    [ 'id' => $id ],
                    [ '%d' ]
                );
            }
        }
        if(!empty($_POST['nombrescontacto'])) {
            foreach($_POST['nombrescontacto'] as $key => $value) {
                $values_contacto = array(
                    'nombre'             => $_POST['nombrescontacto'][$key], 
                    'relacion'           => $_POST['relacionescontacto'][$key],
                    'domicilio'          => $_POST['domicilioscontacto'][$key],
                    'telefono_celular'   => $_POST['celularescontacto'][$key],
                    'telefono_casa'      => $_POST['telcasacontacto'][$key],
                    'telefono_otro'      => $_POST['otrostelefonoscontacto'][$key],
                    'paciente'           => $paciente_id,
                );

                if (empty($_POST['idscontacto'][$key])) {
                    $wpdb->insert( $table_name_contactos, $values_contacto, array(
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                    ));
                } else {
                    $wpdb->update( $table_name_contactos, $values_contacto, array('id' => $_POST['idscontacto'][$key] ), array(
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                    ), '%d');
                }
            }
        }

        // RIESGOS PSICOSOCIALES

        $riesgos_individuales = NULL;
        $riesgos_familiares = NULL;
        $riesgos_entorno = NULL;
        $create_riesgos = false;

        if (!empty($_POST['riesgos_individuales'])) {
            $riesgos_individuales = implode(",", $_POST['riesgos_individuales']);
            $create_riesgos = true;
        }

        if (!empty($_POST['riesgos_familiares'])) {
            $riesgos_familiares = implode(",", $_POST['riesgos_familiares']);
            $create_riesgos = true;
        }

        if (!empty($_POST['riesgos_entorno'])) {
            $riesgos_entorno = implode(",", $_POST['riesgos_entorno']);
            $create_riesgos = true;
        }

        if ($_POST['editmode'] != '1' && $create_riesgos) {
            $wpdb->insert( $table_name_riesgos,
                array(
                    'individual'    => $riesgos_individuales,
                    'familiar'      => $riesgos_familiares,
                    'entorno'       => $riesgos_entorno,
                    'observaciones' => $_POST['riesgos_observaciones'] ,
                    'paciente'      => $paciente_id,
                ), 
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                )
            );
        } elseif ($create_riesgos) {
            $wpdb->update( $table_name_riesgos,
                array(
                    'individual'    => $riesgos_individuales,
                    'familiar'      => $riesgos_familiares,
                    'entorno'       => $riesgos_entorno,
                    'observaciones' => $_POST['riesgos_observaciones'] ,
                    'paciente'      => $paciente_id,
                ), 
                array('paciente' => $paciente_id ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                ),
                '%d'
            );
        }

        // PSICOTROPICOS
        if (!empty($_POST['sustancias_delete'])) {
            foreach( explode(",", $_POST['sustancias_delete']) as $key => $id) {
                $wpdb->delete(
                    $table_name_psicotropicos,
                    [ 'id' => $id ],
                    [ '%d' ]
                );
            }
        }
        if(!empty($_POST['sustancia'])) {
            foreach($_POST['sustancia'] as $key => $value) {
                $sustancia = $_POST['sustancia'][$key];
                if($_POST['sustancia'][$key] == 'Otro') {
                    $sustancia = $_POST['otrasustancia'][$key];
                }

                $periodo = $_POST['periodo'][$key];
                if($_POST['periodo'][$key] == 'Otro') {
                    $periodo = $_POST['otraperiodo'][$key];
                }

                $unidad = $_POST['unidad'][$key];
                if($_POST['unidad'][$key] == 'Otro') {
                    $unidad = $_POST['otraunidad'][$key];
                }
                $values_psicotropicos = array(
                    'sustancia'          => $sustancia,
                    'añoprimeruso'       => $_POST['añoprimeruso'][$key],
                    'edadprimeruso'      => $_POST['edadprimeruso'][$key],
                    'usoregular'         => $_POST['usoregular'][$key],
                    'periodo'            => $periodo,
                    'unidad'             => $unidad,
                    'abstinenciamaxima'  => $_POST['abstinenciamaxima'][$key],
                    'abstinenciaactual'  => $_POST['abstinenciamaxima'][$key],
                    'viadeuso'           => $_POST['viadeuso'][$key],
                    'fechaultimoconsumo' => $_POST['fechaultimoconsumo'][$key],
                    'paciente'           => $paciente_id,
                );
                if (empty($_POST['idssustancias'][$key])) {
                    $wpdb->insert( $table_name_psicotropicos, $values_psicotropicos, array(
                        '%s',
                        '%d',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                    ));
                } else {
                    $wpdb->update( $table_name_psicotropicos, $values_psicotropicos, array('id' => $_POST['idssustancias'][$key] ), array(
                        '%s',
                        '%d',
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                    ), '%d');
                }
            }
        }

        if(!empty($_POST['adaptabilidad'])) {
            $adaptabilidad = fases_procesar($_POST['adaptabilidad'], $_POST['adaptabilidad-name']);
            $cohesion = fases_procesar($_POST['cohesion'],  $_POST['cohesion-name']);
            $rigidez = fases_procesar($_POST['rigidez'], $_POST['rigidez-name']);
            $apego = fases_procesar($_POST['apego'], $_POST['apego-name']);
            $caos = fases_procesar($_POST['caos'], $_POST['caos-name']);
            $desapego = fases_procesar($_POST['desapego'], $_POST['desapego-name']);

            $values_fases = array(
                'adaptabilidad'      => json_encode($adaptabilidad, JSON_NUMERIC_CHECK),
                'cohesion'           => json_encode($cohesion, JSON_NUMERIC_CHECK),
                'rigidez'            => json_encode($rigidez, JSON_NUMERIC_CHECK),
                'apego'              => json_encode($apego, JSON_NUMERIC_CHECK),
                'caos'               => json_encode($caos, JSON_NUMERIC_CHECK),
                'desapego'           => json_encode($desapego, JSON_NUMERIC_CHECK),
                'paciente'           => $paciente_id,
            );
            if ($_POST['editmode'] != '1') {
                $wpdb->insert( $table_name_esquema_fases, $values_fases, array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                ));
            } else {
                $wpdb->update( $table_name_esquema_fases, $values_fases, array('paciente' => $paciente_id ), array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%d',
                ), '%d');
            }
        }

        $values_fad = array(
            'solucion_problemas'           => $_POST['solucion_problemas'],
            'comunicacion'                 => $_POST['comunicacion'],
            'respuesta_afectiva'           => $_POST['respuesta_afectiva'],
            'involucramiento_afectivo'     => $_POST['involucramiento_afectivo'],
            'control_del_comportamiento'   => $_POST['control_del_comportamiento'],
            'funcionamiento_general'       => $_POST['funcionamiento_general'],
            'interpretacion_general'       => $_POST['interpretacion_general'],
            'paciente'                     => $paciente_id,
        );
        if ($_POST['editmode'] != '1') {
            $wpdb->insert( $table_name_fad, $values_fad, array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
            ));
        } else {
            $wpdb->update( $table_name_fad, $values_fad, array('paciente' => $paciente_id ), array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
            ), '%d');
        }

        if(!empty($_POST['nota_progreso'])) {
            $values_notas = array(
                'nota_progreso'                => $_POST['nota_progreso'],
                'fecha'                        => current_time( 'mysql' ),
                'paciente'                     => $paciente_id,
            );
            $wpdb->insert( $table_notas_progreso, $values_notas, array(
                '%s',
                '%s',
                '%d',
            ));
        }
        if(!empty($_POST['evaluacion_psicologica'])) {
            $values_evaluaciones_psicologicas = array(
                'evaluacion_psicologica'       => $_POST['evaluacion_psicologica'],
                'fecha'                        => current_time( 'mysql' ),
                'paciente'                     => $paciente_id,
            );
            $wpdb->insert( $table_evaluaciones_psicologicas, $values_evaluaciones_psicologicas, array(
                '%s',
                '%s',
                '%d',
            ));
        }
        
        if(!empty($_POST['responsables'])) {
            foreach($_POST['responsables'] as $key => $value) {
                $values_responsable = array(
                    'responsable'       => $_POST['responsables'][$key],
                    'paciente'          => $paciente_id,
                );
                if (empty($_POST['id-responsables'][$key])) {
                    $wpdb->insert( $table_responsables, $values_responsable, array(
                        '%s',
                        '%d',
                    ));
                }
            }
        }
        if (!empty($_POST['responsables_delete'])) {
            foreach( explode(",", $_POST['responsables_delete']) as $key => $id) {
                $wpdb->delete(
                    $table_responsables,
                    [ 'id' => $id ],
                    [ '%d' ]
                );
            }
        }

        foreach ($_FILES['archivos']['name'] as $key => $value) {
            if ($_FILES['archivos']['name'][$key]) {
                $file = array(
                    'name'     => $_FILES['archivos']['name'][$key],
                    'type'     => $_FILES['archivos']['type'][$key],
                    'tmp_name' => $_FILES['archivos']['tmp_name'][$key],
                    'error'    => $_FILES['archivos']['error'][$key],
                    'size'     => $_FILES['archivos']['size'][$key]
                );
                $movefile = wp_handle_upload($file, array('test_form' => FALSE));
                if ( $movefile && ! isset( $movefile['error'] ) ) {
                    $url = parse_url($movefile['url'])['path'];
                    $values_archivos_adjuntos = array(
                        'nombre'                       => $file['name'],
                        'archivo_adjunto'              => $url,
                        'fecha'                        => current_time( 'mysql' ),
                        'paciente'                     => $paciente_id,
                    );
                    $wpdb->insert( $table_archivos_adjuntos, $values_archivos_adjuntos, array(
                        '%s',
                        '%s',
                        '%s',
                        '%d',
                    ));
                } else {
                    echo $movefile['error'];
                    $error = true;
                }
            }
        }

        if ($error != true) {
            echo $_POST['nombre'] . ' guardado correctamente.<br><br><a href="/paciente/?paciente=' . $paciente_id . '">Ver</a><br><br><a href="/pacientes">Ver Todos Los Pacientes</a>';
        } else {
            echo '<br><br><a href="/paciente/?paciente=' . $paciente_id . '">Ver</a><br><br><a href="/pacientes">Ver Todos Los Pacientes</a>';
        }
    }

    function fases_procesar($array, $extras) {
        $new = array();
        foreach($array as $index => $value) {
            if ($index > 2) {
                foreach($extras as $extra_index => $extra) {
                    $new[$index + $extra_index] = array('nombre' => $extra, 'valor' => $value);
                }
            } else {
                $new[$index] = $value;
            }
        }
        return $new;
    }
}
