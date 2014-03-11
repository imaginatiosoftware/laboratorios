<?php
  /**
   * The SQLQuery class is intended to be used to access the database.
   */
  class SQLQuery {
    protected static $_dbHandle;
    protected static $_table;
    protected static $_model;
    protected $_result;

    /**
     * This function its currently a placeholder. It just calls the _connect
     * method.
     * @todo quitar esta función, de ser posible
     */
    protected static function connect() {
      static::_connect( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
      static::$_model = strtolower( get_called_class() );
      static::$_table = static::$_model . "s";
    }

    /** 
     * Connects to database
     * @param string $address The address of the database
     * @param string $account The account to access the database
     * @param string $pwd     The account's password to access the database
     * @param string $name    The databas's name
     */
    protected static function _connect( $address, $account, $pwd, $name ) {
      /** 
       * Open a database connection
       * @todo manejar error mediante excepciones?
       */
      static::$_dbHandle = new PDO( "mysql:host=$address;dbname=$name",
                                  $account, $pwd);
    }

    /** 
     * Disconnects from database
     */
    protected static function disconnect() {
      static::$_dbHandle = null;
    }

    /**
     * Fetches all of the model's instances stored in the database
     * @return array Returns an array with the instances fetched.
     * @todo DRY!!!!
     */
    public static function selectAll() {
      static::connect();

      $model_map = json_decode(
        file_get_contents(
          ROOT . DS . "config" . DS . "db" . DS . strtolower( static::$_model )
               . "_mapper.json"
        ),
        true
      );

      $query = "SELECT * FROM {$model_map['table']}";
      $prepared_query = static::$_dbHandle->prepare( $query );
      $prepared_query->execute();

      $result_models = array();
      $results = $prepared_query->fetchAll( PDO::FETCH_ASSOC );

      foreach ( $results as $result ) {
        $result_model = new static::$_model;

        foreach (
          $model_map['attributes'] as $attribute_key => $attribute_value
        ) {
          if ( array_key_exists( $attribute_key, $result ) ) {
            $result_model->_attributes[$attribute_key] = $result[$attribute_key];
          }

          /**
           * One-on-one relationship. One object belongs to another. The
           * containing object has a reference to the object conatined.
           */
          if ( $attribute_key == "has_one" ) {
            foreach ( $model_map['attributes']['has_one'] as $has_one_key => $has_one_value ) {
              $class_name = ucwords($has_one_value);

              $proxy = new ProxyModel( $class_name, $result[$has_one_value . '_id'] );
              $result_model->_attributes[$has_one_key] = $proxy;
            }
          }

          /**
           * Many-to-one, one-to-many, many-to-one relationships. Objects are
           * referenced throught an intermediate table in the database.
           */
          if ( $attribute_key == "has_many" ) {
            foreach ( $model_map['attributes']['has_many'] as $has_many_key => $has_many_value ) {
              $class_name = ucwords($has_many_value['clase']);
              $proxy_collection = array();

              if ( isset( $has_many_value['table'] ) ) {
                $query = "select rueda_id from cosos_ruedas where coso_id = :id";
              } else {
                $query = "select id from ruedas where coso_id = :id";
              }

              $prepared_query = static::$_dbHandle->prepare( $query );
              $prepared_query->execute(array( "id" => $result['id'] ));

              $result_ids = $prepared_query->fetchAll( PDO::FETCH_ASSOC );

              foreach ( $result_ids as $result_id ) {
                array_push($proxy_collection, new ProxyModel( $class_name, $result_id['id'] ));
              }

              $result_model->_attributes[$has_many_key] = $proxy_collection;
            }
          }

          /**
           * One-on-one, many-to-one relationships. This indicates an object
           * belongs to another. May or may not have a reference of the
           * containing object on the database.
           */
          if ( $attribute_key == "belongs_to" ) {
            foreach ( $model_map['attributes']['belongs_to'] as $owner_key => $owner_value ) {
              $class_name = ucwords($owner_value);

              /**
               * Check if the owner is referenced on this object.
               */
              if ( isset( $result[ $owner_value . '_id' ] ) ) {
                $owner_id = $result[ $owner_value . '_id' ];
              }

              /**
               * If not found yet, check the database to retrieve the owner
               * reference throught this object's id
               */
              if ( !isset( $owner_id ) ) {
                $owner_map = json_decode(
                  file_get_contents(
                    ROOT . DS . "config" . DS . "db" . DS . $owner_value
                         . "_mapper.json"
                  ),
                  true
                );

                if ( isset($owner_map['has_one']) 
                     && isset($owner_map['has_one'][static::$_model])
                ) {
                  $query = "SELECT id FROM {$owner_map['table']} "
                           . "WHERE {static::$_model}_id = :model_id";
                  $prepared_statement = static::$_dbHandle->prepare( $query );
                  $prepared_statement->execute(
                    array( "model_id" => $result['id'] )
                  );

                  $owner_result = $prepared_statement->fetch(
                      PDO::FETCH_ASSOC
                  );
                  $owner_id = $owner_result['id'];
                }
              }

              /**
               * Last case, it's a many-to-one relationship. Look for the owner
               * object on the intermediate table.
               */
              if ( !isset( $owner_id ) ) {
                $tabla_intermedia = array( static::$_table, $owner_map['tabla'] );
                echo join( "_", $tabla_intermedia ) . "<br/>";

                $query = "SELECT {$owner_value}_id FROM {tabla_intermedia}"
                         . "WHERE {}_id = :model_id";
                $prepared_statement = static::$_dbHandle->prepare( $query );
                $prepared_statement->execute( array( "model_id" => $result['id'] ) );

                $owner_result = $prepared_statement->fetch( PDO::FETCH_ASSOC );
                $owner_id = $owner_result[$owner_value . '_id'];
              }

              $proxy = new ProxyModel( $class_name, $owner_id );
              $result_model->_attributes[$owner_key] = $proxy;
            }
          }
        }

        array_push( $result_models, $result_model );
      }

      $prepared_query->closeCursor();
      static::disconnect();

      return $result_models; 
    }

    /**
     * Fetches an object stores in the database
     * @param  int   $id The id to look for
     * @return mixed     Returns an instance of the current model if 
     *                   successful or null if it isn't found in the database
     * @todo             Ver como hacer para las tablas intermedias. ¿Se podría
     *                   hacer que la tabla intermedia sea en orden alfabético?
     *                   Ej.: tabla_a_tabla_b, tabla_a y tabla_b tienen una
     *                   relación.
     */
    public static function select( $id ) {
      static::connect();

      $model_map = json_decode(
        file_get_contents(
          ROOT . DS . "config" . DS . "db" . DS
               . strtolower( static::$_model ) . "_mapper.json"
        ),
        true
      );

      $query = "select * from {$model_map['table']} where id = :id";
      $prepared_query = static::$_dbHandle->prepare( $query );
      $prepared_query->execute(array( "id" => $id ));

      $result = $prepared_query->fetch( PDO::FETCH_ASSOC );
      $result_model = new static::$_model;

      if(is_array($result)){
        foreach ( $model_map['attributes'] as $attribute_key => $attribute_value ) {
          if ( array_key_exists( $attribute_key, $result ) ) {
            $result_model->_attributes[$attribute_key] = $result[$attribute_key];
          }

          /**
           * One-on-one relationship. One object belongs to another. The
           * containing object has a reference to the object conatined.
           */
          if ( $attribute_key == "has_one" ) {
            foreach ( $model_map['attributes']['has_one'] as $has_one_key => $has_one_value ) {
              $class_name = ucwords($has_one_value);

              $proxy = new ProxyModel( $class_name, $result[$has_one_value . '_id'] );
              $result_model->_attributes[$has_one_key] = $proxy;
            }
          }

          /**
           * Many-to-one, one-to-many, many-to-one relationships. Objects are
           * referenced throught an intermediate table in the database.
           */
          if ( $attribute_key == "has_many" ) {
            foreach ( $model_map['attributes']['has_many'] as $has_many_key => $has_many_value ) {
              $class_name = ucwords($has_many_value['clase']);
              $proxy_collection = array();

              if ( isset( $has_many_value['tabla'] ) ) {
                $query = "select rueda_id from cosos_ruedas where coso_id = :id";
              } else {
                $query = "select id from ruedas where coso_id = :id";
              }

              $prepared_query = static::$_dbHandle->prepare( $query );
              $prepared_query->execute(array( "id" => $id ));

              $result_ids = $prepared_query->fetchAll( PDO::FETCH_ASSOC );

              foreach ( $result_ids as $result_id ) {
                array_push($proxy_collection, new ProxyModel( $class_name, $result_id['id'] ));
              }

              $result_model->_attributes[$has_many_key] = $proxy_collection;
            }
          }

          /**
           * One-on-one, many-to-one relationships. This indicates an object
           * belongs to another. May or may not have a reference of the
           * containing object on the database.
           */
          if ( $attribute_key == "belongs_to" ) {
            foreach ( $model_map['attributes']['belongs_to'] as $owner_key => $owner_value ) {
              $class_name = ucwords($owner_value);

              /**
               * Check if the owner is referenced on this object.
               */
              if ( isset( $result[ $owner_value . '_id' ] ) ) {
                $owner_id = $result[ $owner_value . '_id' ];
              }

              /**
               * If not found yet, check the database to retrieve the owner
               * reference throught this object's id
               */
              if ( !isset( $owner_id ) ) {
                $owner_map = json_decode(
                  file_get_contents(
                    ROOT . DS . "config" . DS . "db" . DS . $owner_value
                         . "_mapper.json"
                  ),
                  true
                );

                if ( isset($owner_map['has_one']) 
                     && isset($owner_map['has_one'][static::$_model])
                ) {
                  $query = "SELECT id FROM {$owner_map['table']} "
                           . "WHERE {static::$_model}_id = :model_id";
                  $prepared_statement = static::$_dbHandle->prepare( $query );
                  $prepared_statement->execute(
                    array( "model_id" => $result['id'] )
                  );

                  $owner_result = $prepared_statement->fetch(
                      PDO::FETCH_ASSOC
                  );
                  $owner_id = $owner_result['id'];
                }
              }

              /**
               * Last case, it's a many-to-one relationship. Look for the owner
               * object on the intermediate table.
               */
              if ( !isset( $owner_id ) ) {
                $tabla_intermedia = array( static::$_table, $owner_map['tabla'] );
                echo join( "_", $tabla_intermedia ) . "<br/>";

                $query = "SELECT {$owner_value}_id FROM {tabla_intermedia}"
                         . "WHERE {}_id = :model_id";
                $prepared_statement = static::$_dbHandle->prepare( $query );
                $prepared_statement->execute( array( "model_id" => $result['id'] ) );

                $owner_result = $prepared_statement->fetch( PDO::FETCH_ASSOC );
                $owner_id = $owner_result[$owner_value . '_id'];
              }

              $proxy = new ProxyModel( $class_name, $owner_id );
              $result_model->_attributes[$owner_key] = $proxy;
            }
          }
        }
      }

      $prepared_query->closeCursor();
      static::disconnect();
      return $result_model; 
    }

    /**
     * Fetches an instance of the model that fullfils the conditions
     * @param  string  $key The attribute being filtered
     * @param  mixed   $val The value the attribute must have
     * @return mixed        The first instance that matches. Returns null if
     *                      it doesn't match
     * @todo                Devolver un modelo como en select()
     */
    public static function select_where( $key , $val ) {
      static::connect();
      $query = "select * from " . static::$_table . " where :key = :val";
      $prepared_query = static::$_dbHandle->prepare( $query );
      $prepared_query->execute(array( "key" => $key , "val" => $val ));

      $result = $prepared_query->fetch( PDO::FETCH_ASSOC );
      $result_model = new static::$_model;

      if( is_array( $result ) ){
        foreach ( $result as $attribute_key => $attribute_value ) {
          echo $attribute_key . ": " . $attribute_value . "<br/>";
          $result_model->_attributes[$attribute_key] = $attribute_value;
        }
      }

      $prepared_query->closeCursor();
      static::disconnect();
      return $result_model;
    }

    /**
     * Fetches all the model's instances that match the conditions
     * @param  string $key The matching attribute's name
     * @param  string $val The value the attribute must have
     * @return array       A collection of the retrieved instances
     * @todo               Devolver una colección de modelos como en selectAll()
     */
    public static function select_where_all( $key , $val ) {
      static::connect();
      $query = "select * from $this->_table where $key = :val";
      $prepared_query = static::$_dbHandle->prepare( $query );
      $prepared_query->execute( array( "val" => $val ) );

      /**  @todo cambiar esto para que devuelva instancias del modelo  */
      $result_model = $prepared_query->fetchAll(
        PDO::FETCH_CLASS, static::$_model
      );

      $prepared_query->closeCursor();
      static::disconnect();
      return $result_model;
    }

    /**
     * Saves an instance of the model in the database or updates an existing
     * one.
     * @todo fijarse si anda, tal vez se rompió
     */
    public function save() {
      static::connect();
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

      static::disconnect();
    }

    /**
     * Custom SQL Query
     * @todo removerla o cambiarla
     */
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

    /** 
     * Get number of rows
     * @todo removerla o cambiarla
     */
    function getNumRows() {
      return mysql_num_rows( $this->_result );
    }

    /**
     * Free resources allocated by a query
     * @todo removerla, el uso de PDO deja esto obsoleto
     */
    function freeResult() {
      mysql_free_result( $this->_result );
    }

    /** 
     * Get error string
     * @todo puede ser útil, depende si usamos excepciones o no
     */
    function getError() {
      return mysql_error( $this->_dbHandle );
    }

    /**
     * Class destructor. Forces a disconnection when destroyed.
     */
    function __destruct() {
      static::disconnect();
    }
  }
?>
