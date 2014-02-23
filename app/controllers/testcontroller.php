<?php 
  class TestController extends Controller{
    function tests () {
      $this->_template->set( "tests", $this->model->selectAll() );
      //$this->_template->set( "tests", $this->model->select_where_all("id",3) );
    }

    function show ( $params ) {
      //$this->_template->set( "test", $this->model->select( $params[0] ) );
      $this->_template->set( "test", $this->model->select_where( "id" , $params["id"] ) );
    }

    function sumar ( $params ) {
      $value = $params["sumandoA"] + $params["sumandoB"];

      $this->_template->set( "suma", $value );
      $this->_template->set( "uno", $params["sumandoA"] );
      $this->_template->set( "dos", $params["sumandoB"] );
      //$this->_template->set( "test", $this->model->select(1) );

      //echo "El resultado de sumar $values_str es: $value<br/>";
      //echo "<a href=\"/laboratorios/test/sumar/9/8\">Sumar 9+8</a>";
    }
  }
?>
