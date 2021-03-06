<?php
/**
 * Servicios para reportes
 *
 * @package	 expedientes-qi
 * @since    1.0.1
 */
function array_flatten($array) { 
    if (!is_array($array)) { 
      return false; 
    } 
    $result = array(); 
    foreach ($array as $key => $value) { 
      if (is_array($value)) { 
        $result = array_merge($result, array_flatten($value)); 
      } else { 
        $result[$key] = $value; 
      } 
    } 
    return $result; 
  }
//  Servicio para reporte de todos los pacientes
if ( ! function_exists( 'expedientes_reporte_pacientes' ) ) {
    add_action( 'wp_ajax_nopriv_expedientes_reporte_pacientes', 'expedientes_reporte_pacientes' );
    add_action( 'wp_ajax_expedientes_reporte_pacientes', 'expedientes_reporte_pacientes' );

    function expedientes_reporte_pacientes() {
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

        foreach( $pacientes as $key => $value) {
            $id = $pacientes[$key]['id'];
            $responsables = $wpdb->get_results(
                "SELECT responsable FROM $table_responsables WHERE paciente = $id", 
                'ARRAY_N'
            );
            $usuarios = array();
            $responsables = array_flatten($responsables);
            foreach( $responsables as $key_responsable => $responsable) {
                $query = new WP_User_Query( 
                    array(
                        'search'         => '*'.esc_attr( $responsable ).'*',
                        'search_columns' => array( 'user_login', 'user_nicename' )
                    )
                );
                $usuario = $query->get_results()[0];
                $firstname = get_user_meta($usuario->ID, 'first_name', true);
                $lastname = get_user_meta($usuario->ID, 'last_name', true);
                $nombrefinal = '';
                if ($firstname != '') {
                    $nombrefinal = $firstname;
                    if ($lastname != '' ) {
                        $nombrefinal = $firstname . ' ' . $lastname;
                    }
                } else {
                    $nombrefinal = $usuario->user_login;
                }
                array_push($usuarios,  $nombrefinal );
            }
            $pacientes[$key]['responsables'] = implode('<br/>', $usuarios);
        }
		
		wp_send_json($pacientes);
    }
}
//  Servicio para reporte de ficha de identificacion (datos personales)
if ( ! function_exists( 'expedientes_reporte_ficha_identificacion' ) ) {
    add_action( 'wp_ajax_nopriv_expedientes_reporte_ficha_identificacion', 'expedientes_reporte_ficha_identificacion' );
    add_action( 'wp_ajax_expedientes_reporte_ficha_identificacion', 'expedientes_reporte_ficha_identificacion' );

    function expedientes_reporte_ficha_identificacion() {
        global $wpdb;
        $table_pacientes = $wpdb->prefix . "expedientes_pacientes";
        $table_contactos = $wpdb->prefix . "expedientes_personas_contacto";
        $table_riesgos = $wpdb->prefix . "expedientes_riesgos_psicosociales";
        $table_sustancias = $wpdb->prefix . "expedientes_psicotropicos";
        $paciente_id = $_POST['id'];
		$paciente = $wpdb->get_results(
            "SELECT * FROM $table_pacientes WHERE id = '{$paciente_id}'", 
            'ARRAY_A'
        )[0];

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

		wp_send_json($paciente);
    }
}

if ( ! function_exists( 'expedientes_eliminar_nota_progreso' ) ) {
    add_action( 'wp_ajax_nopriv_expedientes_eliminar_nota_progreso', 'expedientes_eliminar_nota_progreso' );
    add_action( 'wp_ajax_expedientes_eliminar_nota_progreso', 'expedientes_eliminar_nota_progreso' );

    function expedientes_eliminar_nota_progreso() {
        global $wpdb;
        $id = $_POST['id'];
        $table_notas_progreso = $wpdb->prefix . "expedientes_notas_progreso";
        $wpdb->delete(
            $table_notas_progreso,
            [ 'id' => $id ],
            [ '%d' ]
        );
        echo 'ok';
        die();
    }
}

if ( ! function_exists( 'expedientes_eliminar_archivo_adjunto' ) ) {
    add_action( 'wp_ajax_nopriv_expedientes_eliminar_archivo_adjunto', 'expedientes_eliminar_archivo_adjunto' );
    add_action( 'wp_ajax_expedientes_eliminar_archivo_adjunto', 'expedientes_eliminar_archivo_adjunto' );

    function expedientes_eliminar_archivo_adjunto() {
        global $wpdb;
        $path = $_POST['path'];
        $id = $_POST['id'];
        $table_archivos_adjuntos = $wpdb->prefix . "expedientes_archivos_adjuntos";
        $wpdb->delete(
            $table_archivos_adjuntos,
            [ 'id' => $id ],
            [ '%d' ]
        );
        unlink(get_home_path() . $path);
        echo 'ok';
        die();
    }
}

if ( ! function_exists( 'expedientes_eliminar_evaluacion_psicologica' ) ) {
    add_action( 'wp_ajax_nopriv_expedientes_eliminar_evaluacion_psicologica', 'expedientes_eliminar_evaluacion_psicologica' );
    add_action( 'wp_ajax_expedientes_eliminar_evaluacion_psicologica', 'expedientes_eliminar_evaluacion_psicologica' );

    function expedientes_eliminar_evaluacion_psicologica() {
        global $wpdb;
        $id = $_POST['id'];
        $table_evaluaciones_psicologicas = $wpdb->prefix . "expedientes_evaluaciones_psicologicas";
        $wpdb->delete(
            $table_evaluaciones_psicologicas,
            [ 'id' => $id ],
            [ '%d' ]
        );
        echo 'ok';
        die();
    }
}

if ( ! function_exists( 'expedientes_eliminar_paciente' ) ) {
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
}

if ( ! function_exists( 'expedientes_get_responsables' ) ) {
    add_action( 'wp_ajax_nopriv_expedientes_get_responsables', 'expedientes_get_responsables' );
    add_action( 'wp_ajax_expedientes_get_responsables', 'expedientes_get_responsables' );

    function expedientes_get_responsables() {
        global $wpdb;
        $search = $_POST['search'];
        $query = new WP_User_Query( 
            array(
                'search'         => '*'.esc_attr( $search ).'*',
                'search_columns' => array( 'user_login', 'user_nicename' )
            )
        );

        $query2 = new WP_User_Query(
            array(
              'meta_query' => array(
              'relation' => 'OR',
                array(
                  'key' => 'first_name',
                  'value' => $search,
                  'compare' => 'LIKE'
                ),
                array(
                  'key' => 'last_name',
                  'value' => $search,
                  'compare' => 'LIKE'
                )
              )
            )
           );

        $results = array_merge($query->get_results(),$query2->get_results());
        $data = array();
        $index = 0;
        foreach ( array_unique($results, SORT_REGULAR) as $user ) {
            $data[$index]['Value'] = $user->user_login;
            $data[$index]['Label'] = $user->user_login . ' - ' . get_user_meta($user->ID, 'first_name', true) . ' ' . get_user_meta($user->ID, 'last_name', true);
            $index++;
        }
        wp_send_json($data);
    }
}

if ( ! function_exists( 'expedientes_reporte_notas_evaluaciones' ) ) {
    add_action( 'wp_ajax_nopriv_expedientes_reporte_notas_evaluaciones', 'expedientes_reporte_notas_evaluaciones' );
    add_action( 'wp_ajax_expedientes_reporte_notas_evaluaciones', 'expedientes_reporte_notas_evaluaciones' );

    function expedientes_reporte_notas_evaluaciones() {
        global $wpdb;
        $paciente_id = $_POST['id'];
        $mode = $_POST['mode'];
        $table_name_evaluaciones = $wpdb->prefix . "expedientes_evaluaciones_psicologicas";
        $table_name_notas = $wpdb->prefix . "expedientes_notas_progreso";
        $table_pacientes = $wpdb->prefix . "expedientes_pacientes";
        $paciente = $wpdb->get_results(
            "SELECT * FROM $table_pacientes WHERE id = '{$paciente_id}'", 
            'ARRAY_A'
        )[0];

        $data = null;
        if($mode == 'notas_progreso') {
            $data = $wpdb->get_results(
                "SELECT * FROM $table_name_notas WHERE paciente = '{$paciente_id}'", 
                'ARRAY_A'
            );
        }
        if($mode == 'evaluaciones_psicologicas') {
            $data = $wpdb->get_results(
                "SELECT * FROM $table_name_evaluaciones WHERE paciente = '{$paciente_id}'", 
                'ARRAY_A'
            );
        }
        wp_send_json(array('paciente' => $paciente, 'data' => $data));
    }
}

if ( ! function_exists( 'expedientes_get_nota_progreso' ) ) {
    add_action( 'wp_ajax_nopriv_expedientes_get_nota_progreso', 'expedientes_get_nota_progreso' );
    add_action( 'wp_ajax_expedientes_get_nota_progreso', 'expedientes_get_nota_progreso' );

    function expedientes_get_nota_progreso() {
        global $wpdb;
        $nota_id = $_POST['id'];
        $table_name_notas = $wpdb->prefix . "expedientes_notas_progreso";
        $data = $wpdb->get_results(
            "SELECT * FROM $table_name_notas WHERE id = '{$nota_id}'", 
            'ARRAY_A'
        );
        wp_send_json($data[0]);
    }
}

if ( ! function_exists( 'expedientes_get_evaluacion_psicologica' ) ) {
    add_action( 'wp_ajax_nopriv_expedientes_get_evaluacion_psicologica', 'expedientes_get_evaluacion_psicologica' );
    add_action( 'wp_ajax_expedientes_get_evaluacion_psicologica', 'expedientes_get_evaluacion_psicologica' );

    function expedientes_get_evaluacion_psicologica() {
        global $wpdb;
        $evaluacion_id = $_POST['id'];
        $table_name_evaluaciones = $wpdb->prefix . "expedientes_evaluaciones_psicologicas";
        $data = $wpdb->get_results(
            "SELECT * FROM $table_name_evaluaciones WHERE id = '{$evaluacion_id}'", 
            'ARRAY_A'
        );
        wp_send_json($data[0]);
    }
}