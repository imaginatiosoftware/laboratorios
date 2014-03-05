<?php
  class SQLQuery {
    protected $_dbHandle;
    protected $_result;
    protected $_table;
    protected $_model;

    /** 
     * Connects to database
     * @param string $address The address of the database
     * @param string $account The account to access the database
     * @param string $pwd     The account's password to access the database
     * @param string $name    The databas's name
     */
    function connect( $address, $account, $pwd, $name ) {
      /** 
       * Abro conexión con la base de datos por medio de PDO
       * @todo manejar error mediante excepciones?
       */
      $this->_dbHandle = new PDO( "mysql:host=$address;dbname=$name",
                                  $account, $pwd);
    }

    /** Disconnects from database **/
    function disconnect() {
      $this->_dbHandle = null;
    }

    public function save( ) {
      $values = "";
      $key_values = "";

      foreach($this->_attributes as $key => $value){
        if($key_values == ""){
          $key_values = $key ." = ". $value ;
        }else{
          $key_values = $key_values .", ". $key ." = ". $value;
        }
        if($values == ""){
          $values = ":". $key ;
        }else{
          $values = $values .", :". $key;
        }
      }

      if( isset( $this->_attributes["id"] ) ) {
        $query = "update tabla set $key_values where id = :id";
        $prepared_query = $this->_dbHandle->prepare( $query );
        $prepared_query->execute( array( "id" => $this->_attributes["id"] ) );
      }else{
        $query = "insert into $_table values($values)";
        $prepared_query = $this->_dbHandle->prepare( $query );
        $prepared_query->execute($this->_attributes);
        $this->_attributes["id"] = $this->_dbHandle->lastInsertId('id');
      }

    }

    function selectAll() {
      $query = "select * from $this->_table";
      $prepared_query = $this->_dbHandle->prepare( $query );
      $prepared_query->execute();

      $result_model = $prepared_query->fetchAll(PDO::FETCH_CLASS, $this->_model);

      $prepared_query->closeCursor();
      return $result_model; 
    }

    /**
     * Fetches an object stores in the database
     * @param int    $id  The id to look for
     * @return mixed Returns an instance of the current model if successful or
     *               null if it isn't found in the database
     */
    function select( $id ) {
      $model_map = json_decode(
        file_get_contents(
          ROOT . DS . "config" . DS . "db" . DS . strtolower( $this->_model ) . "_mapper.json"
        ),
        true
      );

      echo var_dump( $model_map );

      $query = "select * from {$model_map['table']} where id = :id";
      $prepared_query = $this->_dbHandle->prepare( $query );
      $prepared_query->execute(array( "id" => $id ));

      $result = $prepared_query->fetch( PDO::FETCH_ASSOC );
      $result_model = new $this->_model;

      if(is_array($result)){
        foreach ( $model_map['attributes'] as $attribute_key => $attribute_value ) {
          if ( array_key_exists( $attribute_key, $result ) ) {
            //acá para cuando son atributos comunes, simplemente se cargan como si nada
            $result_model->_attributes[$attribute_key] = $result[$attribute_key];
          }

          if ( $attribute_key == "has_one" ) {
            //acá para cuando es relación uno a uno
            foreach ( $model_map['attributes']['has_one'] as $has_one_key => $has_one_value ) {
              echo "$has_one_key<br/>";
              //cargar un proxy
            }
          }

          if ( $attribute_key == "has_many" ) {
            //acá para cuando es de muchos a uno o a muchos
            foreach ( $model_map['attributes']['has_many'] as $has_many_key => $has_many_value ) {
              echo "$has_many_key<br/>";
              //cargar los proxies correspondientes
            }
          }

          if ( $attribute_key == "belongs_to" ) {
            //acá cuando es de muchos a uno o a muchos
            foreach ( $model_map['attributes']['belongs_to'] as $owner_key => $owner_value ) {
              echo "$owner_key<br/>";
              //cargar un proxy
            }
          }
        }
      }

      $prepared_query->closeCursor();
      return $result_model; 
    }

    function select_where( $key , $val ) {
      $query = "select * from $this->_table where $key = :val";
      $prepared_query = $this->_dbHandle->prepare( $query );
      $prepared_query->execute(array( "val" => $val ));
      
      $result = $prepared_query->fetch( PDO::FETCH_ASSOC );
      $result_model = new $this->_model;

      if( is_array( $result ) ){
        foreach ( $result as $attribute_key => $attribute_value ) {
          $result_model->$attribute_key = $attribute_value;
        }
      }

      $prepared_query->closeCursor();
      return $result_model;
    }

    function select_where_all( $key , $val ) {
      $query = "select * from $this->_table where $key = :val";
      $prepared_query = $this->_dbHandle->prepare( $query );
      $prepared_query->execute(array( "val" => $val ));

      $result_model = $prepared_query->fetchAll(PDO::FETCH_CLASS, $this->_model);

      $prepared_query->closeCursor();
      return $result_model;
    }

    /** Custom SQL Query **/
    function query( $query, $singleResult = 0 ) {
      $this->_result = mysql_query( $query, $this->_dbHandle );

      if ( preg_match( "/select/i", $query ) ) {
        $result = array();
        $table = array();
        $field = array();
        $tempResults = array();
        $numOfFields = mysql_num_fields( $this->_result );

        for ( $i = 0; $i < $numOfFields; ++$i ) {
          array_push( $table,mysql_field_table( $this->_result, $i ) );
          array_push( $field, mysql_field_name( $this->_result, $i ) );
        }

        while ( $row = mysql_fetch_row( $this->_result ) ) {
          for ( $i = 0; $i < $numOfFields; ++$i ) {
            $table[$i] = trim( ucfirst( $table[$i] ), "s" );
            $tempResults[$table[$i]][$field[$i]] = $row[$i];
          }

          if ( $singleResult == 1 ) {
            mysql_free_result( $this->_result );
            return $tempResults;
          }

          array_push( $result, $tempResults );
        }

        mysql_free_result( $this->_result );
        return $result;
      }
    }

    /** Get number of rows **/
    function getNumRows() {
      return mysql_num_rows( $this->_result );
    }

    /** Free resources allocated by a query **/
    function freeResult() {
      mysql_free_result( $this->_result );
    }

    /** Get error string **/
    function getError() {
      return mysql_error( $this->_dbHandle );
    }

    function __destruct() {
      disconnect();
    }
  }
?>
