<?php
  $requestURI = explode('/', $_SERVER['REQUEST_URI']);
  
  try{
    if (!file_exists("./app/controllers/". $requestURI[2] ."_controller.php")){
      throw new Exception("Routing Error.");
    }
    else{
      require_once "./app/controllers/". $requestURI[2] ."_controller.php";
      if(!function_exists ($requestURI[3])){
        throw new Exception("Routing Error.");
      }else{
        call_user_func($requestURI[3]);
      }
    }
  }catch(Exception $ex){
    echo $requestURI[2] ." missmatched.<br/>";
    echo $ex->getMessage();
  }
?>
