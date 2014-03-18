<?php
  class UsuarioController extends Controller {
    function login ( $params ) {
      session_start();

      if ( isset( $params['flash'] ) ) {
        $this->set( 'flash', $params['flash'] );
      }

      if ( isset( $_SESSION['current_user'] ) ) {
        $this->redirect_to( 'dashboard', $params );
      }
    }

    function authenticate ( $params ) {
      $mensaje = array( "error" => "Usuario y/o contrase&ntilde;a incorrectos" );
      $usuario = Usuario::select_where( "email", $params['username'] );

      if ( $usuario != null ) {
        if ( $usuario->password == $params['password'] ) {
          session_start();
          $_SESSION['current_user'] = $usuario;
          $mensaje = array( "success" => "¡Bienvenido {$usuario->email}!" );
        }
      }
      $params['flash'] = $mensaje;

      $this->redirect_to( 'login', $params );
    }

    function logoff ( $params ) {
      session_start();
      session_destroy();

      $this->redirect_to( 'login', $params );
    }

    //Recordar que list es palabra reservada
    function usuarios ( $params ) {
    }

    function dashboard( $params ) {
      session_start();

      if ( isset( $params['flash'] ) ) {
        $this->set( 'flash', $params['flash'] );
      }

      if ( !isset( $_SESSION['current_user'] ) ) {
        $this->redirect_to( 'login', $params );
      }
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
