-- ****************** EXPEDIENTES QI ******************;
-- ***************************************************;
DROP TABLE %TABLE_PREFIX%pacientes;
DROP TABLE %TABLE_PREFIX%psicotropicos;
DROP TABLE %TABLE_PREFIX%personas_contacto;
DROP TABLE %TABLE_PREFIX%riesgos_psicosociales;
-- ************************************** %TABLE_PREFIX%pacientes

CREATE TABLE %TABLE_PREFIX%pacientes
(
    id                 INT NOT NULL AUTO_INCREMENT,
    fotografia         VARCHAR(500) NOT NULL ,
    nombre             VARCHAR(100) NOT NULL ,
    fechadenacimiento  DATE NOT NULL ,
    edad               INT NOT NULL ,
    escolaridad        VARCHAR(250) ,
    ocupacion          VARCHAR(250) ,
    estadocivil        VARCHAR(50) ,
    cantidadhijos      INT ,
    domicilio          VARCHAR(200) ,
    ciudaddeorigen     VARCHAR(200) ,
    telefono           VARCHAR(45) ,
    email              VARCHAR(100) ,
    enfermedades       TEXT ,
    alergias           TEXT ,
    responsable        VARCHAR(50) NOT NULL ,
    fecha_creacion     DATETIME NOT NULL,
    fecha_modificacion DATETIME,
PRIMARY KEY (id)
)%CHARSET_COLLATE%;


-- ************************************** %TABLE_PREFIX%psicotropicos

CREATE TABLE %TABLE_PREFIX%psicotropicos
(
    id                 INT NOT NULL AUTO_INCREMENT,
    sustancia          VARCHAR(100) NOT NULL ,
    añoprimeruso       INT,
    edadprimeruso      INT,
    usoregular         VARCHAR(45),
    periodo            VARCHAR(45),
    unidad             VARCHAR(45),
    abstinenciamaxima  VARCHAR(45),
    abstinenciaactual  VARCHAR(45),
    viadeuso           VARCHAR(45),
    fechaultimoconsumo DATE ,
    paciente           INT NOT NULL ,

PRIMARY KEY (id),
KEY fkIdx_67 (paciente),
CONSTRAINT FK_67 FOREIGN KEY fkIdx_67 (paciente) REFERENCES %TABLE_PREFIX%pacientes (id)
)%CHARSET_COLLATE%;


-- ************************************** %TABLE_PREFIX%personas_contacto

CREATE TABLE %TABLE_PREFIX%personas_contacto
(
    id                INT NOT NULL AUTO_INCREMENT,
    nombre            VARCHAR(100) NOT NULL ,
    relacion          VARCHAR(45) ,
    domicilio         VARCHAR(200) ,
    telefono_celular  VARCHAR(100) ,
    telefono_casa     VARCHAR(100) ,
    telefono_otro     VARCHAR(100) ,
    paciente          INT NOT NULL ,

PRIMARY KEY (id),
KEY fkIdx_49 (paciente),
CONSTRAINT FK_49 FOREIGN KEY fkIdx_49 (paciente) REFERENCES %TABLE_PREFIX%pacientes (id) ON DELETE CASCADE ON UPDATE CASCADE
)%CHARSET_COLLATE%;


-- ************************************** %TABLE_PREFIX%riesgos_psicosociales

CREATE TABLE %TABLE_PREFIX%riesgos_psicosociales
(
    id            INT NOT NULL AUTO_INCREMENT,
    individual    TEXT ,
    familiar      TEXT ,
    entorno       TEXT ,
    observaciones TEXT ,
    paciente      INT NOT NULL ,

PRIMARY KEY (id),
KEY fkIdx_39 (paciente),
CONSTRAINT FK_39 FOREIGN KEY fkIdx_39 (paciente) REFERENCES %TABLE_PREFIX%pacientes (id) ON DELETE CASCADE ON UPDATE CASCADE
)%CHARSET_COLLATE%;


-- ************************************** %TABLE_PREFIX%esquema_fases

CREATE TABLE %TABLE_PREFIX%esquema_fases
(
    id             INT NOT NULL AUTO_INCREMENT,
    adaptabilidad  TEXT,
    cohesion       TEXT,
    rigidez        TEXT,
    apego          TEXT,
    caos           TEXT,
    desapego       TEXT,
    paciente       INT NOT NULL ,
PRIMARY KEY (id),
KEY fkIdx_35 (paciente),
CONSTRAINT FK_35 FOREIGN KEY fkIdx_35 (paciente) REFERENCES %TABLE_PREFIX%pacientes (id) ON DELETE CASCADE ON UPDATE CASCADE
)%CHARSET_COLLATE%;

-- ************************************** %TABLE_PREFIX%fad

CREATE TABLE %TABLE_PREFIX%fad
(
    id                           INT NOT NULL AUTO_INCREMENT,
    solucion_problemas           TEXT,
    comunicacion                 TEXT,
    respuesta_afectiva           TEXT,
    involucramiento_afectivo     TEXT,
    control_del_comportamiento   TEXT,
    funcionamiento_general       TEXT,
    interpretacion_general       TEXT,
    paciente                     INT NOT NULL ,
PRIMARY KEY (id),
KEY fkIdx_36 (paciente),
CONSTRAINT FK_36 FOREIGN KEY fkIdx_36 (paciente) REFERENCES %TABLE_PREFIX%pacientes (id) ON DELETE CASCADE ON UPDATE CASCADE
)%CHARSET_COLLATE%;

-- ************************************** %TABLE_PREFIX%notas_progreso

CREATE TABLE %TABLE_PREFIX%notas_progreso
(
    id                           INT NOT NULL AUTO_INCREMENT,
    nota_progreso                TEXT,
    fecha                        DATETIME,
    paciente                     INT NOT NULL ,
PRIMARY KEY (id),
KEY fkIdx_37 (paciente),
CONSTRAINT FK_37 FOREIGN KEY fkIdx_37 (paciente) REFERENCES %TABLE_PREFIX%pacientes (id) ON DELETE CASCADE ON UPDATE CASCADE
)%CHARSET_COLLATE%;

-- ************************************** %TABLE_PREFIX%archivos_adjuntos

CREATE TABLE %TABLE_PREFIX%archivos_adjuntos
(
    id                           INT NOT NULL AUTO_INCREMENT,
    nombre                       VARCHAR(100),
    archivo_adjunto              TEXT,
    fecha                        DATETIME,
    paciente                     INT NOT NULL ,
PRIMARY KEY (id),
KEY fkIdx_38 (paciente),
CONSTRAINT FK_38 FOREIGN KEY fkIdx_38 (paciente) REFERENCES %TABLE_PREFIX%pacientes (id) ON DELETE CASCADE ON UPDATE CASCADE
)%CHARSET_COLLATE%;
