<?php 
  class Controller {
    protected $_model;
    protected $_controller;
    protected $_action;
    protected $_template;

    /**
     * Class constructor
     * @param  String $model      The name of the controller's model
     * @param  String $controller The name of the controller itself
     * @param  String $action     The name of the action being invoked
     */
    function __construct( $model, $controller, $action ) {
      $this->_controller = $controller;
      $this->_action     = $action;
      $this->_model      = $model;

      if ( class_exists( $model ) ){
        $this->model     = new $model;
      }

      $this->_template   = new Template( $controller, $action );
    }

    /**
     * Sets a variable that is accesible from the view
     * @param  String $name   The variable name
     * @param  String $value  The name of the controller itself
     */
    function set ( $name, $value ) {
      $this->_template->set( $name, $value );
    }

    /**
     * Redirects the call to another action.
     * @param  mixed $action
     * @param  string $params
     * @param  string $controller
     * @throws Exception
     */
    function redirect_to( $action, $params = null, $controller = null ) {
      /** 
       * Check if the controller parameter is passed, and if it is passed,
       * checks whether it is the current controller or another one.
       */
      if ( $controller && $controller != $this->_controller ) {
        /**  Current template is removed */
        $this->_template = null;
        /**  The controller is loaded  */
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
        /**
         * If the controller is the same or it wasn't specified, it assumes its
         * a reference to an action in the current controller
         */
        $this->_action = $action;
        /**  The template is changed  */
        $this->_template = new Template( $this->_controller, $action );
        /**  And finally, the action is called  */
        $this->$action( $params );
      }
    }

    /**  Removes the controller's view  */
    function not_render () {
      $this->_template = null;
    }

    /**  
     * Sets the view to be rendered.
     * @param mixed  $view       The view to be rendered
     * @param string $controller If set, the view's contoller. Defaults to the
     *                           current controller
     */
    function render_view ( $view, $controller = null ) {
      if ( !isset( $controller ) ) {
        $controller = $this->_controller;
      }
      $this->_template = new Template( $controller, $view );
    }

    /**  
     * Renders a json file
     * @param mixed $data The data to be served as json.
     */
    function render_json ( $data ){
      $this->not_render();
      header('Content-Type: application/json');
      echo json_encode( $data );
    }

    /**
     * Class destructor. Renders its view, if there is one.
     */
    function __destruct() {
      if( isset( $this->_template ) ) {
        $this->_template->render();
      }
    }
  }
?>
