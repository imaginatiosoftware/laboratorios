<?php 
  require_once ROOT . DS . 'app' . DS . 'base/controller.class.php';

  class TestController extends Controller{
    function sumar ( $v ) {
      $value = 0;
      $values_str = join( ", ", $v );

      for($i = 0;$i < count($v); $i++){
        $value += intval($v[$i]);
      }

      $this->_template->set( "suma", $value );
      $this->_template->set( "sumandos", $value );

      //echo "El resultado de sumar $values_str es: $value<br/>";
      //echo "<a href=\"/laboratorios/test/sumar/9/8\">Sumar 9+8</a>";
    }
  }
?>
