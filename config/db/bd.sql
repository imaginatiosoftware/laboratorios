DROP DATABASE IF EXISTS laboratorios_development;
CREATE DATABASE laboratorios_development;

USE laboratorios_development;

CREATE TABLE permisos (
  id          INT              NOT NULL AUTO_INCREMENT,
  descripcion VARCHAR  ( 255 ) NOT NULL,
  habilitado  TINYINT  ( 1   ) NOT NULL,
  PRIMARY KEY ( id )
);

CREATE TABLE roles (
  id          INT              NOT NULL AUTO_INCREMENT,
  descripcion VARCHAR  ( 255 ) NOT NULL,
  habilitado  TINYINT  ( 1   ) NOT NULL,
  PRIMARY KEY ( id )
);

CREATE TABLE roles_permisos (
  rol_id      INT NOT NULL,
  permiso_id  INT NOT NULL,
  FOREIGN KEY ( rol_id     ) REFERENCES roles    ( id ),
  FOREIGN KEY ( permiso_id ) REFERENCES permisos ( id ),
  PRIMARY KEY ( rol_id, permiso_id )
);

CREATE TABLE usuarios (
  id                   INT             NOT NULL AUTO_INCREMENT,
  nombre               VARCHAR ( 255 ) NOT NULL,
  apellido             VARCHAR ( 255 ) NOT NULL,
  segundo_apellido     VARCHAR ( 255 ) NOT NULL,
  cedula               VARCHAR ( 255 ) NOT NULL,
  nro_caja_profesional VARCHAR ( 255 ) NOT NULL,
  direccion            VARCHAR ( 255 ) NOT NULL,
  telefono             VARCHAR ( 255 ) NOT NULL,
  email                VARCHAR ( 255 ) NOT NULL,
  rol_id               INT             NOT NULL,
  habilitado           TINYINT ( 1   ) NOT NULL,
  password             VARCHAR ( 255 ) NOT NULL,
  FOREIGN KEY          ( rol_id )      REFERENCES roles( id ),
  PRIMARY KEY ( id )
);