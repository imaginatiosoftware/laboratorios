<?php
  class DBClasses {
    protected $_dbHandle;

    /**
     * Connects to the database
     */
    public function connect( $address, $account, $pwd, $name ) {
      //abro conexión con la base de datos por medio de PDO
      //manejar error mediante excepciones
      $this->_dbHandle = new PDO( "mysql:host=$address;dbname=$name",
                                  $account, $pwd);
    }

    /**
     * Disconnects from database
     */
    //PDO es atajado por garbage collector
    function disconnect() {
      $this->_dbHandle = null;
    }

    /**
     * Fetches the classes stored in the database
     * @return array|NULL Returns an array with the classes retrieved or NULL 
     *                    no class was found
     */
    public function import_all_db_classes() {
      $query = "select * from raw_classes_code";
      $prepared_query = $this->_dbHandle->prepare( $query );
      $prepared_query->execute();

      $result = $prepared_query->fetchAll( PDO::FETCH_ASSOC );

      if( is_array( $result ) ){
        return $result;
      }else{
        return null;
      }
    }

    /**
     * Stores a new class on the database.
     * @param string $code       The class' code
     * @param string $type       The class' type
     * @param string $class_name The class' name
     */
    public function new_db_clas( $code , $type , $class_name ) {
      $query = "insert into raw_classes_code values(:code,:type,:class_name)";
      $prepared_query = $this->_dbHandle->prepare( $query );
      $prepared_query->execute(array( "code" => $code, "type" => $type, "class_name" => $class_name ));
    }

    /**
     * Class destructor. Forces a disconnection on destroy.
     */
    public function __destroy() {
      $this->disconnect();
    }
  }
?>
