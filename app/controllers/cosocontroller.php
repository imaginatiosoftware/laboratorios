<?php 
  class CosoController extends Controller {
    function cosos ( $params ) {
      $this->set( "cosos", Coso::selectAll() );
    }

    function cosos_post ( $params ) {
      $this->set( "flash", array( "notice" => "POST!" ) );
      $this->set( "coso", $params['coso'] );
    }

    function cosos_put ( $params ) {
    }

    function cosos_delete ( $params ) {
    }

    function show ( $params ) {
      $this->set( "coso", Coso::select( $params['id'] ) );
    }
  }
?>
