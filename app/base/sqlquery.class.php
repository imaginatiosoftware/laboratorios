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
     * @todo         cambiar la funcion para que devuelva modelos según el mapper,
     *               para referencias, ver la función select
     */
    public static function selectAll() {
      static::connect();

      $query = "select * from " . static::$_table;
      $prepared_query = static::$_dbHandle->prepare( $query );
      $prepared_query->execute();

      $result_model = $prepared_query->fetchAll(
        PDO::FETCH_CLASS, static::$_model
      );

      $prepared_query->closeCursor();

      static::disconnect();
      return $result_model; 
    }

    /**
     * Fetches an object stores in the database
     * @param  int   $id The id to look for
     * @return mixed     Returns an instance of the current model if 
     *                   successful or null if it isn't found in the database
     */
    public static function select( $id ) {
      static::connect();
      $model_map = json_decode(
        file_get_contents(
          ROOT . DS . "config" . DS . "db" . DS . strtolower( static::$_model ) . "_mapper.json"
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
            //acá para cuando son atributos comunes, simplemente se cargan como si nada
            $result_model->_attributes[$attribute_key] = $result[$attribute_key];
          }

          if ( $attribute_key == "has_one" ) {
            //acá para cuando es relación uno a uno
            foreach ( $model_map['attributes']['has_one'] as $has_one_key => $has_one_value ) {
              //cargar un proxy
              $class_name = ucwords($has_one_value);

              $proxy = new ProxyModel( $class_name, $result[$has_one_value . '_id'] );
              $result_model->_attributes[$has_one_key] = $proxy;
            }
          }

          if ( $attribute_key == "has_many" ) {
            //acá para cuando es de muchos a uno o a muchos
            foreach ( $model_map['attributes']['has_many'] as $has_many_key => $has_many_value ) {
              //cargar los proxies correspondientes
              $class_name = ucwords($has_many_value['clase']);
              $proxy_model_name = "{$class_name}Proxy";
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

          if ( $attribute_key == "belongs_to" ) {
            //acá cuando es de muchos a uno
            foreach ( $model_map['attributes']['belongs_to'] as $owner_key => $owner_value ) {
              //cargar un proxy
              $clase = ucwords($owner_value);
              $proxy_model_name = "{$clase}Proxy";
              //$result_model->_attributes[$owner_key] = $prxy_model_name;
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
