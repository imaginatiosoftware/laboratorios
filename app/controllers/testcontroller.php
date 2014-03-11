<?php
  class TestController extends Controller {
    function check_json(){}

    function tests() {
      $this->_template->set( "tests", $this->model->selectAll() );
    }

    function show ( $params ) {
      $this->_template->set( "test", Test::select( $params["id"] ) );
    }

    function create () {
    }

    function save ( $params ) {
      $this->_template = new Template( "test", "tests" );
      $this->_template->set("flash", "No se va a crear nada." );
    }

    function sumar ( $params ) {
      $value = $params["sumandoA"] + $params["sumandoB"];

      $this->_template->set( "suma", $value );
      $this->_template->set( "uno",  $params["sumandoA"] );
      $this->_template->set( "dos",  $params["sumandoB"] );
    }
  }
?>
