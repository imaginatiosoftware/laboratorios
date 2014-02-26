<?php 
  class CosoController extends Controller {
    function cosos ( $params ) {
      $this->set( "flash", array( "notice" => "Coso!" ) );
      $this->set( "cosovo", "Acá hay un Cosovo" );
      $this->set( "coso", $params['coso'] );
    }

    function cosos_post ( $params ) {
      $this->set( "flash", array( "notice" => "POST!" ) );
      $this->set( "coso", $params['coso'] );
    }

    function redirect () {
      $this->redirect_to( "tests", null, "test" );
    }
  }
?>
