<?php 
  function trim_value ( &$value ) {
    $value = trim( $value );
  }

  $raw_paths = explode( "route: ", trim(file_get_contents( "routes.txt", true )) );
  array_shift( $raw_paths );

  foreach ( $raw_paths as $raw_path ) {
    $processed_path = explode( ",",
      //str_replace( array( " ", "controller:", "action:", "\"" ), "", $raw_path )
      $raw_path
    );

    array_walk( $processed_path, 'trim_value' );

    $processed_paths[] = $processed_path;
  }

  $paths = $processed_paths;
?>
