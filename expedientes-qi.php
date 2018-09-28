<?php
/**
 * Plugin Name:       Expedientes QI
 * Plugin URI:        https://github.com/NTHINGs/expedientes-qi
 * Description:       Coding shortcodes in a plugin with maintainable code practices.
 * Version:           1.0.1
 * Author:            Mauricio Martinez
 * Author URI:        https://github.com/NTHINGs
 * License:           MIT
 * License URI:       https://github.com/NTHINGs/expedientes-qi/blob/master/LICENSE
 * Text Domain:       expedientes-qi
 *
 * @link              https://github.com/NTHINGs/expedientes-qi
 * @package           expedientes-qi
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define global constants.
 *
 * @since 1.0.0
 */
// Plugin version.
if ( ! defined( 'ABS_VERSION' ) ) {
	define( 'ABS_VERSION', '1.0.0' );
}

if ( ! defined( 'ABS_NAME' ) ) {
	define( 'ABS_NAME', trim( dirname( plugin_basename( __FILE__ ) ), '/' ) );
}

if ( ! defined( 'ABS_DIR' ) ) {
	define( 'ABS_DIR', WP_PLUGIN_DIR . '/' . ABS_NAME );
}

if ( ! defined( 'ABS_URL' ) ) {
	define( 'ABS_URL', WP_PLUGIN_URL . '/' . ABS_NAME );
}

/**
 * Link.
 *
 * @since 1.0.0
 */
if ( file_exists( ABS_DIR . '/shortcodes/shortcode-print.php' ) ) {
	require_once( ABS_DIR . '/shortcodes/shortcode-print.php' );
}
if ( file_exists( ABS_DIR . '/shortcodes/agregar-paciente.php' ) ) {
	require_once( ABS_DIR . '/shortcodes/agregar-paciente.php' );
}

add_action('wp_enqueue_scripts','expedientes_qi_init');

function expedientes_qi_init() {
	wp_deregister_script('jquery');
	wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js', array(), null, true);
	wp_register_script( 'popper', '//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js', array( 'jquery' ), '3.3.1', false );
	wp_enqueue_script( 'bootstrap', '//stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js', array( 'jquery', 'popper' ), '3.3.1', false );
	wp_enqueue_script( 'gijgo', '//cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js', array( 'jquery' ), '3.3.1', false );
	wp_enqueue_style( 'gijgo', '//cdn.jsdelivr.net/npm/gijgo@1.9.10/css/gijgo.min.css');
    wp_register_script( 'jspdf', plugins_url( '/js/jspdf.min.js', __FILE__ ));
	wp_register_script( 'expedientes_qi', plugins_url( '/js/expedientes_qi.js', __FILE__ ));
}

// Create Tables
function create_plugin_database() {
    global $table_prefix, $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	// $sql = str_replace(array("%TABLE_PREFIX%", "%CHARSET_COLLATE%"), array($table_prefix, $charset_collate), file_get_contents( plugin_dir_path(__FILE__) . "/schema.sql" ));
	$sql = "CREATE TABLE IF NOT EXISTS %TABLE_PREFIX%expedientes_pacientes
	(
	 id                INT NOT NULL AUTO_INCREMENT,
	 fotografia        VARCHAR(500) NOT NULL ,
	 nombre            VARCHAR(100) NOT NULL ,
	 fechadenacimiento DATE NOT NULL ,
	 edad              INT NOT NULL ,
	 escolaridad       VARCHAR(250) ,
	 ocupacion         VARCHAR(250) ,
	 estadocivil       VARCHAR(50) ,
	 cantidadhijos     INT ,
	 domicilio         VARCHAR(200) ,
	 ciudaddeorigen    VARCHAR(200) ,
	 telefono          VARCHAR(45) ,
	 email             VARCHAR(100) NOT NULL ,
	 enfermedades      TEXT ,
	 alergias          TEXT ,
	 responsable       VARCHAR(50) NOT NULL ,
	
	PRIMARY KEY (id)
	)%CHARSET_COLLATE%;
	
	
	-- ************************************** %TABLE_PREFIX%expedientes_psicotropicos
	
	CREATE TABLE IF NOT EXISTS %TABLE_PREFIX%expedientes_psicotropicos
	(
	 id                 INT NOT NULL AUTO_INCREMENT,
	 sustancia          VARCHAR(45) NOT NULL ,
	 añoprimeruso       INT,
	 edadprimeruso      INT,
	 usoregular         VARCHAR(45),
	 unidadespordia     INT,
	 unidad             VARCHAR(45),
	 vecespordia        INT,
	 periodo            VARCHAR(45),
	 abstinenciamaxima  VARCHAR(45),
	 abstinenciaactual  VARCHAR(45),
	 viadeuso           VARCHAR(45),
	 fechaultimoconsumo DATE ,
	 paciente           INT NOT NULL ,
	
	PRIMARY KEY (id),
	KEY fkIdx_67 (paciente),
	CONSTRAINT FK_67 FOREIGN KEY fkIdx_67 (paciente) REFERENCES %TABLE_PREFIX%expedientes_pacientes (id)
	)%CHARSET_COLLATE%;
	
	
	-- ************************************** %TABLE_PREFIX%expedientes_personas_contacto
	
	CREATE TABLE IF NOT EXISTS %TABLE_PREFIX%expedientes_personas_contacto
	(
	 id        INT NOT NULL AUTO_INCREMENT,
	 nombre    VARCHAR(100) NOT NULL ,
	 relacion  VARCHAR(45) ,
	 domicilio VARCHAR(200) ,
	 telefonos VARCHAR(100) ,
	 paciente  INT NOT NULL ,
	
	PRIMARY KEY (id),
	KEY fkIdx_49 (paciente),
	CONSTRAINT FK_49 FOREIGN KEY fkIdx_49 (paciente) REFERENCES %TABLE_PREFIX%expedientes_pacientes (id) ON DELETE CASCADE ON UPDATE CASCADE
	)%CHARSET_COLLATE%;
	
	
	-- ************************************** %TABLE_PREFIX%expedientes_riesgos_psicosociales
	
	CREATE TABLE IF NOT EXISTS %TABLE_PREFIX%expedientes_riesgos_psicosociales
	(
	 id            INT NOT NULL AUTO_INCREMENT,
	 individual    TEXT ,
	 familiar      TEXT ,
	 entorno       TEXT ,
	 observaciones TEXT ,
	 paciente      INT NOT NULL ,
	
	PRIMARY KEY (id),
	KEY fkIdx_39 (paciente),
	CONSTRAINT FK_39 FOREIGN KEY fkIdx_39 (paciente) REFERENCES %TABLE_PREFIX%expedientes_pacientes (id) ON DELETE CASCADE ON UPDATE CASCADE
	)%CHARSET_COLLATE%;
	
	";
	$sql = str_replace(array("%TABLE_PREFIX%", "%CHARSET_COLLATE%"), array($table_prefix, $charset_collate), $sql);
	// $sql = "CREATE TABLE $table_name (
	// 	id INT NOT NULL AUTO_INCREMENT,
	// 	fotografia        VARCHAR(500) NOT NULL ,
	// 	nombre            VARCHAR(100) NOT NULL ,
	// 	fechadenacimiento DATE NOT NULL ,
	// 	edad              INT NOT NULL ,
	// 	escolaridad       VARCHAR(250) ,
	// 	ocupacion         VARCHAR(250) ,
	// 	estadocivil       VARCHAR(50) ,
	// 	cantidadhijos     INT ,
	// 	domicilio         VARCHAR(200) ,
	// 	ciudaddeorigen    VARCHAR(200) ,
	// 	telefono          VARCHAR(45) ,
	// 	email             VARCHAR(100) NOT NULL ,
	// 	enfermedades      TEXT ,
	// 	alergias          TEXT ,
	// 	responsable       VARCHAR(50) NOT NULL ,

	// 	PRIMARY KEY (id)
	// 	) $charset_collate;";
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}
register_activation_hook( __FILE__, 'create_plugin_database' );
