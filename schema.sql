-- ****************** EXPEDIENTES QI ******************;
-- ***************************************************;

DROP TABLE IF EXISTS `%TABLE_PREFIX%psicotropicos`;


DROP TABLE IF EXISTS `%TABLE_PREFIX%personas_contacto`;


DROP TABLE IF EXISTS `%TABLE_PREFIX%riesgos_psicosociales`;


DROP TABLE IF EXISTS `%TABLE_PREFIX%paciente`;

-- ************************************** `%TABLE_PREFIX%paciente`

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%paciente`
(
 `id`                INT NOT NULL AUTO_INCREMENT,
 `fotografia`        VARCHAR(500) NOT NULL ,
 `nombre`            VARCHAR(100) NOT NULL ,
 `fechadenacimiento` DATE NOT NULL ,
 `edad`              INT NOT NULL ,
 `escolaridad`       VARCHAR(250) ,
 `ocupacion`         VARCHAR(250) ,
 `estadocivil`       VARCHAR(50) ,
 `cantidadhijos`     INT ,
 `domicilio`         VARCHAR(200) ,
 `ciudaddeorigen`    VARCHAR(200) ,
 `telefono`          VARCHAR(45) ,
 `email`             VARCHAR(100) NOT NULL ,
 `enfermedades`      TEXT ,
 `alergias`          TEXT NOT NULL ,
 `responsable`       VARCHAR(50) NOT NULL ,

PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


-- ************************************** `%TABLE_PREFIX%psicotropicos`

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%psicotropicos`
(
 `id`                 INT NOT NULL AUTO_INCREMENT,
 `sustancia`          VARCHAR(45) NOT NULL ,
 `añoprimeruso`       INT,
 `edadprimeruso`      INT,
 `usoregular`         VARCHAR(45),
 `unidadespordia`     INT,
 `unidad`             VARCHAR(45),
 `vecespordia`        INT,
 `periodo`            VARCHAR(45),
 `abstinenciamaxima`  VARCHAR(45),
 `abstinenciaactual`  VARCHAR(45),
 `viadeuso`           VARCHAR(45),
 `fechaultimoconsumo` DATE ,
 `paciente`           INT NOT NULL ,

PRIMARY KEY (`id`),
KEY `fkIdx_67` (`paciente`),
CONSTRAINT `FK_67` FOREIGN KEY `fkIdx_67` (`paciente`) REFERENCES `%TABLE_PREFIX%paciente` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


-- ************************************** `%TABLE_PREFIX%personas_contacto`

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%personas_contacto`
(
 `id`        INT NOT NULL AUTO_INCREMENT,
 `nombre`    VARCHAR(100) NOT NULL ,
 `relacion`  VARCHAR(45) ,
 `domicilio` VARCHAR(200) ,
 `telefonos` VARCHAR(100) ,
 `paciente`  INT NOT NULL ,

PRIMARY KEY (`id`),
KEY `fkIdx_49` (`paciente`),
CONSTRAINT `FK_49` FOREIGN KEY `fkIdx_49` (`paciente`) REFERENCES `%TABLE_PREFIX%paciente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;


-- ************************************** `%TABLE_PREFIX%riesgos_psicosociales`

CREATE TABLE IF NOT EXISTS `%TABLE_PREFIX%riesgos_psicosociales`
(
 `id`            INT NOT NULL AUTO_INCREMENT,
 `individual`    TEXT ,
 `familiar`      TEXT ,
 `entorno`       TEXT ,
 `observaciones` TEXT ,
 `paciente`      INT NOT NULL ,

PRIMARY KEY (`id`),
KEY `fkIdx_39` (`paciente`),
CONSTRAINT `FK_39` FOREIGN KEY `fkIdx_39` (`paciente`) REFERENCES `%TABLE_PREFIX%paciente` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

