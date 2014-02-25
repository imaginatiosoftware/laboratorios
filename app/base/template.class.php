<?php 
  class Template {
    protected $variables = array();
    protected $_controller;
    protected $_action;

    function __construct ( $controller, $action ) {
      $this->_controller = $controller;
      $this->_action     = $action;
    }

    function set( $name, $value ) {
      $this->variables[$name] = $value;
    }

    function render() {
      extract( $this->variables );

      if ( file_exists( ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . 'header.php' ) ) {
        include_once ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . 'header.php';
      } else {
        include_once ROOT . DS . 'app' . DS . 'views' . DS . 'layout/header.php';
      }

      include_once ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . $this->_action . '.php';

      if ( file_exists( ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . 'footer.php' ) ) {
        include_once ROOT . DS . 'app' . DS . 'views' . DS . $this->_controller . DS . 'footer.php';
      } else {
        include_once ROOT . DS . 'app' . DS . 'views' . DS . 'layout/footer.php';
      }
    }
  }
?>
