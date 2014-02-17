<?php 

  function sumar(&$v)
  {
    $value = 0;
    for($i = 3;$i < count($requestURI); $i++){
      echo($requestURI);
      $value = $value + intval($requestURI[$i]);
    }
  //echo(var_dump(explode('/', $login_path)));
  //echo(var_dump($requestURI));
  echo("El resultado de sumar es: ");
  echo($value);
  }
  
?>
