<?php
  class SQLQuery {
    protected $_dbHandle;
    protected $_result;
    protected $_table;
    protected $_model;

    /** Connects to database **/
    function connect( $address, $account, $pwd, $name ) {
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

    function selectAll() {
      $query = "select * from $this->_table";
      $prepared_query = $this->_dbHandle->prepare( $query );
      $prepared_query->execute();

      $result_model = $prepared_query->fetchAll(PDO::FETCH_CLASS, $this->_model);

      $prepared_query->closeCursor();
      return $result_model; 
    }

    function select( $id ) {
      $query = "select * from $this->_table where id = :id";
      $prepared_query = $this->_dbHandle->prepare( $query );
      $prepared_query->execute(array( "id" => $id ));

      $result = $prepared_query->fetch( PDO::FETCH_ASSOC );
      $result_model = new $this->_model;

      if(is_array($result)){
        foreach ( $result as $attribute_key => $attribute_value ) {
          $result_model->$attribute_key = $attribute_value;
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
  }
?>
