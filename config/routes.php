<?php
  function process_path( $url ) {
    require_once 'path_parser.php';
    $result = null;

    //if( $url ){
      $splited_request = explode( "/", $url );

      foreach($paths as $path){
        $splited_path = explode( "/", $path[0] );
        $found = false;
        $variables = new ArrayObject( array(), ArrayObject::STD_PROP_LIST );

        if( count( $splited_path ) == count( $splited_request ) ){
          $found = true;

          for($i = 0; $i < count( $splited_path ); $i++){
            $param = explode(":", $splited_path[$i]);

            if(count($param) == 2 ){
              if($splited_request[$i] != ""){
                $variables[(string)$param[1]] = $splited_request[$i];
              }else{
                $found = false;
                break;
              }
            }else{
              if ($splited_path[$i] != $splited_request[$i]){
                $found = false;
                break;
              }
            }
          }

          if ($found == true){
            $result = array();

            if ( count( $path ) > 3 ) {
              $result['variables']['method'] = $path[3];
            }

            $result["controller"] = $path[1];
            $result["action"] = $path[2];
            $result["variables"] = $variables;
            //foreach ( $variables as $key => $value ) {
            //  echo $key. " = " .$value;
            //  echo "</br>";
            //}
          }
        }
      }
    //} else {
     // echo "ROOT_PATH";
    //}

    return $result;
  }
?>
