DROP laboratorios_development IF EXISTS;
CREATE DATABASE laboratorios_development;

CREATE TABLE usuarios (
  id                   LONG            NOT NULL AUTO_INCREMENT,
  nombre               VARCAHR ( 255 ) NOT NULL,
  apellido             VARCHAR ( 255 ) NOT NULL,
  segundo_apellido     VARCHAR ( 255 ) NOT NULL,
  cedula               VARCHAR ( 255 ) NOT NULL,
  nro_caja_profesional VARCHAR ( 255 ) NOT NULL,
  direccion            VARCHAR ( 255 ) NOT NULL,
  telefono             VARCHAR ( 255 ) NOT NULL,
  email                VARCHAR ( 255 ) NOT NULL,
  rol_id               LONG            NOT NULL,
  habilitado           TINY INT        NOT NULL,
  FOREIGN KEY          rol_id          REFERENCES roles( id ),
  PRIMARY KEY ( id )
);