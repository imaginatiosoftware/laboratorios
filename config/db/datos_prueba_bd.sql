--INSERT INTO permisos () VALUES ();

INSERT INTO permisos ( descripcion, habilitado )
  VALUES ( "/laboratorios/usuario/usuarios", 1 );

INSERT INTO roles ( descripcion, habilitado )
  VALUES ( "administrador", 1 );

INSERT INTO roles_permisos ( rol_id, permiso_id )
  VALUES ( 1, 1 );

INSERT INTO usuarios (
    nombre,
    apellido,
    segundo_apellido,
    cedula,
    nro_caja_profesional,
    direccion,
    telefono,
    email,
    rol_id,
    habilitado
  ) VALUES (
    "Test",
    "Test",
    "TestDos",
    "5555555",
    "01",
    "Una direccion",
    "47222222",
    "test@test.test",
    1,
    1
  );