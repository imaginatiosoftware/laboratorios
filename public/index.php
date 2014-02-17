<?php
  $requestURI = explode('/', $_SERVER['REQUEST_URI']);
  $file_path = $_SERVER['DOCUMENT_ROOT']. "/laboratorios/app/controllers/". $requestURI[2] ."_controller.php";
  echo($_SERVER['DOCUMENT_ROOT']);
  echo("<br/>");
  echo($_SERVER['SCRIPT_FILENAME']);
  echo("<br/>");
  echo($file_path);
  echo("<br/>");
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
  }
?>
