<?php
  function process_path( $url ) {
    require_once 'path_parser.php';
    $result = null;
    if( !isAsset($url) ){
      $splited_request = explode( "/", $url );

      foreach($paths as $path){
        $splited_path = explode( "/", $path[0] );
        $found = false;
        $variables = new ArrayObject( array(), ArrayObject::STD_PROP_LIST );

        if ( isset( $path[3] ) ) {
          $method = $path[3];
        } else {
          $method = "ALL";
        }

        if( count( $splited_path ) == count( $splited_request ) 
            && ( strtoupper( $method ) == $_SERVER['REQUEST_METHOD'] 
                 || strtoupper( $method ) == "ALL"
               )
        ){
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
            $result['method'] = $_SERVER['REQUEST_METHOD'];
            $result["type"] = "action";
            $result["controller"] = $path[1];
            $result["action"] = $path[2];
            $result["variables"] = $variables;

            /*if ( $result['method'] == "GET" ) {
              echo var_dump($_GET) . "<hr/>";
            } elseif ( $result['method'] == "POST" ) {
              echo var_dump($_POST) . "<hr/>";
            } elseif ( $result['method'] == "PUT" ) {
              //echo var_dump($_PUT) . "<hr/>";
            } elseif ( $result['method'] == "DELETE" ) {
              echo implode(", ", $_DELETE) . "<hr/>";
            }*/

            //foreach ( $variables as $key => $value ) {
            //  echo $key. " = " .$value;
            //  echo "</br>";
            //}
          }
        }
      }
    } else {
      //if it si an asset it will change the routing system.
      $result = array();
      $result["type"] = "asset";
      
    }

    return $result;
  }
  
  function isAsset( $url ) {
    $result = false;
    $splited_request = explode( "/", $url );
    if (count($splited_request)>1){ 
      if ( $splited_request[0] == "assets" || $splited_request[1] == "assets"){
        $result = true;
      }
    }
    return $result;
  }
  
 ?>
