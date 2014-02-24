<?php 
  class Controller {
    protected $_model;
    protected $_controller;
    protected $_action;
    protected $_template;

    function __construct( $model, $controller, $action ) {
      $this->_controller = $controller;
      $this->_action     = $action;
      $this->_model      = $model;

      $this->model      = new $model; 
      $this->_template   = new Template( $controller, $action );
    }

    function set ( $name, $value ) {
      $this->_template->set( $name, $value );
    }

    function redirect_to( $action, $params = null, $controller = null ) {
      if ( $controller && $controller != $this->_controller ) {
        $this->_template = null;
        $controllerName = $controller;
        $controller     = ucwords( $controllerName ) . "Controller";
        $model          = $controllerName;
        $dispatch       = new $controller( $model, $controllerName, $action );

        if ( method_exists( $controller, $action ) ) {
          call_user_func( array($dispatch, $action), $params );
        } else {
          throw new Exception( "No se encontró el método $action", 2 );
        }
      } else {
        $this->_action = $action;
        $this->_template = new Template( $this->_controller, $action );
        $this->$action();
      }
    }

    function render ( $view ) {
      $this->_template = new Template( $this->_controller, $view );
    }

    function __destruct() {
      if( isset( $this->_template ) ) {
        $this->_template->render();
      }
    }
  }
?>
