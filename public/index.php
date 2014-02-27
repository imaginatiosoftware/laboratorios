<?php
  //url array
  //$requestURI = explode('/', $_SERVER['REQUEST_URI']);
  //echo var_dump( $_SERVER ) . "<br/>";
  //echo var_dump( $_POST ) . "<br/>";
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
?>
