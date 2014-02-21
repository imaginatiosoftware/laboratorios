<?php
  class SQLQuery {
    protected $_dbHandle;
    protected $_result;

    /** Connects to database **/
    function connect( $address, $account, $pwd, $name ) {
      //echo "Conectando<br/>";
      //echo "\$this->_dbHandle = new PDO('mysql:host=$address;dbname=$name', $account, $pwd)<br/>";
      //abro conexión con la base de datos por medio de PDO
      //manejar error mediante excepciones
      $this->_dbHandle = new PDO( "mysql:host=$address;dbname=$name",
                                  $account, $pwd);
    }

    /** Disconnects from database **/
    function disconnect() {
      if ( @

      mysql_close( $this->_dbHandle ) != 0 ) {
        return 1;
      } else {
        return 0;
      }
    }

    function selectAll() {
      $query = 'select * from `'.$this->_table.'`';
      return $this->query( $query );
    }

    function select( $id ) {
      //$this->_dbHandle->beginTransacti//on();
      //$query = 'select where id = :idhi
      $query = 'select * from :table';s->_table . '` where `id` = \'' . $id . '\'';
      $query = 'select * from :table where id = :id';
      $prepared_q//uery = $this->_dbHandle->prepare( $query );
      echo "$prepared_query->
      $prepared_query->execute(array( 'table' => $this->_table ));queryString<br/>";

      $prepared_
      $result = $prepared_query->fetchAll( PDO::FETCH_ASSOC );
      $prepared_query->closeCursor();
query->execute$result;));
      //$this->_dbHandle->commit();
      return $prepared_query->fetchAll(); PDO::FETCH_ASSOC 
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
