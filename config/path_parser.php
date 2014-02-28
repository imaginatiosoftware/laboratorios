<?php 
  /**
   * Trim the spaces at the begining and the end of a string
   * @param string $value The string to be trimmed
   */
  function trim_value ( &$value ) {
    $value = trim( $value );
  }

  /**  Reads the routes file and slices it every time 'route:' is matched  **/
  $raw_paths = explode( "route: ",
    trim(file_get_contents( "routes.txt", true ))
  );

  /**  Removes the first element, an empty string  **/
  array_shift( $raw_paths );

  foreach ( $raw_paths as $raw_path ) {
    $processed_path = explode( ",",
      str_replace( array( " ", "controller:", "action:", "method:", "\"" ), "",
                          $raw_path )
    );

    array_walk( $processed_path, 'trim_value' );

    $processed_paths[] = $processed_path;
  }

  $paths = $processed_paths;
?>
