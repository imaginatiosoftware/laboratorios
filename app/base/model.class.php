<?php
  class Model extends SQLQuery {
    protected static $_table;
    protected static $_model;
    public $_attributes = array();

    function __construct () {
      //$this->connect( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
      //$this->_model = get_class( $this );
      //$this->_table = strtolower( $this->_model ) . "s";
      $this->_attributes = new ArrayObject( array(), ArrayObject::STD_PROP_LIST );
    }

    function __get( $name ) {
      return $this->_attributes[$name];
    }

    function __set( $name, $value ) {
      $this->_attributes[$name] = $value;
    }

    function __destruct() {
    }
  }
?>
