<?php 

  function sumar(&$v)
  {
    for($i = 3;$i < count($requestURI); $i++){
      $value = $value + intval($requestURI[$i]);
    }
  }
  //echo(var_dump(explode('/', $login_path)));
  //echo(var_dump($requestURI));
  echo($value);
  }
  
?>
