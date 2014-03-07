<?php 
  /**
   * The Template class is used to serve a view.
   */
  class Template {
    protected $variables = array();
    protected $_controller;
    protected $_action;

    /**
     * Class constructor. Initializes the instance.
     * @param mixed  $controller The controller instance that owns this template
     * @param string $action     The action to be matched by a view
     */
    function __construct ( $controller, $action ) {
      $this->_controller = $controller;
      $this->_action     = $action;
    }

    /**
     * Sets variables available on the view
     * @param string $name  The variable's name
     * @param mixed  $value The value of the variable
     */
    function set( $name, $value ) {
      $this->variables[$name] = $value;
    }

    /**
     * Loads the view that will be served to the user
     */
    function render() {
      extract( $this->variables );

      /**
       * Loads a header. Defaults to the global header if an specific one isn't
       * found
       */
      if ( file_exists( ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . 'header.php' ) ) {
        include_once ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . 'header.php';
      } else {
        include_once ROOT . DS . 'app' . DS . 'views' . DS . 'layout/header.php';
      }

      /**  Loads the view that matches the action name  */
      include_once ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller .
                   DS . $this->_action . '.php';

      /**
       * Loads a footer. Defaults to the global footer if an specific one isn't
       * found
       */
      if ( file_exists( ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . 'footer.php' ) ) {
        include_once ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . 'footer.php';
      } else {
        include_once ROOT . DS . 'app' . DS . 'views' . DS . 'layout/footer.php';
      }
    }
  }
?>
