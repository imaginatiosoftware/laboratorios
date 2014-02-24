<?php 
  class CosoController extends Controller {
    function cosos () {
      $this->set( "flash", array( "notice" => "Coso!" ) );
      $this->set( "cosovo", "Acá hay un Cosovo" );
    }

    function redirect () {
      //$this->_template = new Template( "test", "tests" );
      //(new TestController( "test", "test", "tests" ))->tests();
      //redirect_to( "test", "tests" );
      $this->redirect_to( "tests", null, "test" );
    }
  }
?>
