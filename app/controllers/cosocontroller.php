<?php 
  class CosoController extends Controller {
    function cosos () {
      $this->set( "flash", array( "notice" => "Coso!" ) );
      $this->set( "cosovo", "Acá hay un Cosovo" );
    }

    function redirect () {
      $this->redirect_to( "tests", null, "test" );
    }
  }
?>
