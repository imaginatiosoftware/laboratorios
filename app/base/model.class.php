<?php
  class Model extends SQLQuery {
    protected $_model;

    //tener en cuenta la función get_class_vars
    function __construct () {
      $this->connect( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
      $this->_model = get_class( $this );
      $this->_table = strtolower( $this->_model ) . "s";
      echo "$this->_table";
    }

    function __destruct() {
    }
  }
?>