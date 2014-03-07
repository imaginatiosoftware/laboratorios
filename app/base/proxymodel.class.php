<?php 
  /**
   * The ProxyModel class loads the real model when required.
   */
  class ProxyModel {
    protected $_real_model_instance;
    protected $_real_model_id;
    protected $_real_model_name;
    protected $_real_model_methods = array();

    /**
     * Class constructor
     * @param string $real_model_name The model's name
     * @param int    $model_id        The model's id
     */
    function __construct( $real_model_name, $model_id ) {
      $this->_real_model_id = $model_id;
      $this->_real_model_name = $real_model_name;
      $this->_real_model_methods = get_class_methods( $real_model_name );
    }

    /**
     * This magic method allows to access the real model's methods without the
     * need to declare those methods on the proxy
     * @param  string $method The method's name
     * @param  mixed  $args   The argument/s for the method
     * @return mixed          Returns the return value of the method invoked
     */
    function __call ( $method, $args ) {
      if ( is_callable( array( $this->_real_model_name, $method ) ) ) {
        if ( !isset( $this->_real_model_instance ) ) {
          $this->_real_model_instance = call_user_func(
            array( $this->_real_model_name, "select"),
            $this->_real_model_id
          );
        }

        return call_user_func( array( $this->_real_model_instance, $method ), $args );
      }
    }
  }
?>
