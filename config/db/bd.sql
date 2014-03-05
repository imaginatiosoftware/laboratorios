CREATE TABLE cosos (
  id               INT             NOT NULL AUTO_INCREMENT,
  nombre_coso      VARCHAR ( 255 ) NOT NULL,
  descripcion_coso VARCHAR ( 255 ) NOT NULL,
  PRIMARY KEY      ( id )
);

CREATE TABLE materiales (
  id          INT             NOT NULL AUTO_INCREMENT,
  descripcion VARCHAR ( 255 ) NOT NULL,
  PRIMARY KEY ( id )
);

CREATE TABLE ruedas (
  id          INT             NOT NULL AUTO_INCREMENT,
  material_id INT             NOT NULL,
  cosos_id    INT             NOT NULL,
  descripcion VARCHAR ( 255 ) NOT NULL,
  FOREIGN KEY ( material_id ) REFERENCES materiales( id ),
  FOREIGN KEY ( cosos_id    ) REFERENCES cosos     ( id ),
  PRIMARY KEY ( id )
);