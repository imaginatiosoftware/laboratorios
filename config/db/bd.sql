CREATE TABLE cosos (
  id               INT             NOT NULL AUTO_INCREMENT,
  nombre_coso      VARCHAR ( 255 ) NOT NULL,
  descripcion_coso VARCHAR ( 255 ) NOT NULL,
  PRIMARY KEY      ( id )
);

CREATE TABLE materials (
  id          INT             NOT NULL AUTO_INCREMENT,
  descripcion VARCHAR ( 255 ) NOT NULL,
  PRIMARY KEY ( id )
);

CREATE TABLE ruedas (
  id          INT             NOT NULL AUTO_INCREMENT,
  material_id INT             NOT NULL,
  coso_id    INT             NOT NULL,
  descripcion VARCHAR ( 255 ) NOT NULL,
  FOREIGN KEY ( material_id ) REFERENCES materials( id ),
  FOREIGN KEY ( coso_id    ) REFERENCES cosos     ( id ),
  PRIMARY KEY ( id )
);
