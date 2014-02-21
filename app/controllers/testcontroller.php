<?php 
  class TestController extends Controller{
    function tests () {
      $this->template->set( "tests", $this->model->selectAll() );
    }

    function show ( $params ) {
      $this->_template->set( "test", $this->model->select( $params[0] ) );
    }

    function sumar ( $v ) {
      $value = 0;
      $values_str = join( ", ", $v );

      for($i = 0;$i < count($v); $i++){
        $value += intval($v[$i]);
      }

      $this->_template->set( "suma", $value );
      $this->_template->set( "sumandos", $values_str );
      $this->_template->set( "test", $this->model->select(1) );

      //echo "El resultado de sumar $values_str es: $value<br/>";
      //echo "<a href=\"/laboratorios/test/sumar/9/8\">Sumar 9+8</a>";
    }
  }
?>
