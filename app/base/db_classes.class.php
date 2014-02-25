<?php
  class DBClasses {
    protected $_dbHandle;

    /** Connects to database **/
    public function connect( $address, $account, $pwd, $name ) {
      //abro conexión con la base de datos por medio de PDO
      //manejar error mediante excepciones
      $this->_dbHandle = new PDO( "mysql:host=$address;dbname=$name",
                                  $account, $pwd);
    }

    /** Disconnects from database **/
    //PDO es atajado por garbage collector
    function disconnect() {
      /*if ( @mysql_close( $this->_dbHandle ) != 0 ) {
        return 1;
      } else {
        return 0;
      }*/
    }

    public function import_all_db_classes() {
      $query = "select * from raw_classes_code";
      $prepared_query = $this->_dbHandle->prepare( $query );
      $prepared_query->execute();

      $result = $prepared_query->fetchAll( PDO::FETCH_ASSOC );
      
      if(is_array($result)){
        return $result;
      }else{
        return null;
      }
    }
  }
?>
    