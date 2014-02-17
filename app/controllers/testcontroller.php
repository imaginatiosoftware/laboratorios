<?php 
  class TestController {
    function sumar($v)
    {
      $value = 0;
      for($i = 3;$i < count($v); $i++){
        echo($v);
        //$value = $value + intval($requestURI[$i]);
      }
    //echo(var_dump(explode('/', $login_path)));
    //echo(var_dump($requestURI));
    echo("El resultado de sumar es: ");
    //echo($value);
    }
  }
?>
