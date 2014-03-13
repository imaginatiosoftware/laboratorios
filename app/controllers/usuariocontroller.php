<?php
  class UsuarioController extends Controller {
    function login ( $params ) {
    }

    function authenticate ( $params ) {
      $usuario = Usuario::select_where( "email", $params['username'] );

      if ( $usuario != null ) {
        echo "¡Bienvenido {$usuario->email}!<hr/>";
      } else {
        echo "No che, no no no!";
      }

      $this->redirect_to( "login", $params );
    }

    function logoff ( $params ) {
    }

    //Recordar que list es palabra reservada
    function usuarios ( $params ) {
      
    }

    function create ( $params ) {
    }

    function edit ( $params ) {
    }

    function save ( $params ) {
    }

    function destroy ( $params ) {
    }
  }
?>
