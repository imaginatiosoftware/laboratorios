<?php
  //load the routes file to match the user request
  try{
    if (!file_exists(ROOT . DS . 'config' . DS . 'routes.php')){
      throw new Exception("Routes file missing. <br />");
    } else {
      require_once (ROOT . DS . 'config' . DS . 'routes.php');
    }
  } catch(Exception $ex){
    echo $ex->getMessage();
    $errors = 1;
  }
  
  function process_url($url) {
  
  }
?>
