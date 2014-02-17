<?php
  //url array
  //$requestURI = explode('/', $_SERVER['REQUEST_URI']);
  $url = $_GET['url'];
  $errors = 0;
  
  define('DS', DIRECTORY_SEPARATOR);
  define('ROOT', dirname(dirname(__FILE__)));
  
  //loading app configuration
  try{
    if (!file_exists(ROOT . DS . 'config' . DS . 'config.php')){
      throw new Exception("Configuration file missing. <br />");
    } else {
      require_once (ROOT . DS . 'config' . DS . 'config.php');
    }
  } catch(Exception $ex){
    echo $ex->getMessage();
    $errors = 1;
  }

  //load the routing engine passing the user request
  try{
    if (!file_exists(ROOT . DS . 'config' . DS . 'router.php')){
      throw new Exception("Router engine missing. <br />");
    } else {
      require_once (ROOT . DS . 'config' . DS . 'router.php');
    }
  } catch(Exception $ex){
    echo $ex->getMessage();
    $errors = 1;
  }

  /*
  // if there are no error loading the app configuration
  if ( $errors == 0 ) {
    // loading controller
    $file_path = ROOT . DS . "app/controllers/". $requestURI[2] ."_controller.php";

    try{
      if (!file_exists($file_path)){
        throw new Exception("Routing Error.");
      }
      else{
        require_once  $file_path;
        if(!function_exists ($requestURI[3])){
          throw new Exception("Routing Error.");
        }else{
          call_user_func($requestURI[3]);
        }
      }
    }catch(Exception $ex){
      echo $requestURI[2] ." missmatched.<br/>";
      echo $ex->getMessage();
      $errors = 1;
    }
  }
  */
?>
