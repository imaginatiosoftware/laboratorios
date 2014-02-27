<?php
  /**  Load the routes file to match the user request  */
  try{
    if (!file_exists(ROOT . DS . 'config' . DS . 'routes.php')){
      throw new Exception("Routes file missing. <br />");
    } else {
      require_once (ROOT . DS . 'config' . DS . 'routes.php');
    }
  } catch(Exception $ex){
    echo $ex->getMessage();
    $errors = 1;
  }

  /** Check if environment is development and display errors **/
  function setReporting() {
    if (DEVELOPMENT_ENVIRONMENT == true) {
      error_reporting(E_ALL);
      ini_set('display_errors','On');
    } else {
      error_reporting(E_ALL);
      ini_set('display_errors','Off');
      ini_set('log_errors', 'On');
      ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
    }
  }

  /** Check for Magic Quotes and remove them **/
  function stripSlashesDeep($value) {
    $value = is_array( $value ) ?
                  array_map('stripSlashesDeep', $value) : stripslashes($value);
    return $value;
  }

  function removeMagicQuotes() {
    if ( get_magic_quotes_gpc() ) {
      $_GET    = stripSlashesDeep($_GET   );
      $_POST   = stripSlashesDeep($_POST  );
      $_COOKIE = stripSlashesDeep($_COOKIE);
    }
  }

  /** Check register globals and remove them **/
  function unregisterGlobals() {
    if (ini_get('register_globals')) {
      $array = array( '_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST',
                                                 '_SERVER', '_ENV', '_FILES' );
      foreach ($array as $value) {
        foreach ($GLOBALS[$value] as $key => $var) {
          echo $key . " " . $var . "<br/>";
          if ($var === $GLOBALS[$key]) {
            unset($GLOBALS[$key]);
          }
        }
      }
    }
  }

  /** Main Call Function **/
  function callHook() {
    global $url;
    $result = process_path( $url ); 
    echo var_dump( $_GET ) . "<hr/>";
    echo var_dump( $_POST);
    //echo file_get_contents("php://input") . "<hr/>";

    if ($result == null){
      echo "No route matches";
    } else if ($result["type"] == "action" ){
      require_once(ROOT . DS . 'app' . DS . 'base' . DS . 'db_classes.class.php');
      $classes_from_database = new DBClasses();
      $classes_from_database->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      $classes_from_db = $classes_from_database->import_all_db_classes();

      foreach ( $classes_from_db as $each_class ) {
        eval($each_class["code"]);
      }

      $controller = $result["controller"];
      $action     = $result["action"];
      $params     = $result["variables"];
      $method     = $result["method"];

      foreach($_GET as $key => $value){
        if ($key != "url"){
          $params[$key]=$value;
        }
      }

      foreach($_POST as $key => $value){
        if ($key != "url"){
          $params[$key]=$value;
        }
      }

      $controllerName = $controller;
      $controller     = ucwords( $controller );
      $model          = rtrim( $controller, 's' );
      $controller    .= 'Controller';

      $dispatch = new $controller( $model, $controllerName, $action );

      if ( method_exists( $controller, $action ) ) {
        call_user_func( array($dispatch, $action), $params );
      } else {
        throw new Exception( "No se encontró el método $action", 2 );
      }
    } else if ($result["type"] == "asset" ) {
      require_once(ROOT . DS . 'app' . DS . 'base' . DS . 'assets.class.php');
      $asset = new Assets( $url );
      $asset->render(); 
    }
  } 

  /** Autoload any classes that are required **/
  function __autoload( $className ) {
    if (file_exists(ROOT . DS . 'library' . DS . strtolower($className) . '.class.php')) {
      require_once(ROOT . DS . 'library' . DS . strtolower($className) . '.class.php');

    } else if (file_exists(ROOT . DS . 'app' . DS . 'base' . DS . strtolower($className) . '.class.php')) {
      require_once(ROOT . DS . 'app' . DS . 'base' . DS . strtolower($className) . '.class.php');

    } else if (file_exists(ROOT . DS . 'app' . DS . 'controllers' . DS . strtolower($className) . '.php')) {
      require_once(ROOT . DS . 'app' . DS . 'controllers' . DS . strtolower($className) . '.php');

    } else if (file_exists(ROOT . DS . 'app' . DS . 'models' . DS . strtolower($className) . '.php')) {
      require_once(ROOT . DS . 'app' . DS . 'models' . DS . strtolower($className) . '.php');

    } else {
      /* Error Generation Code Here */
    }
  }

  setReporting();
  removeMagicQuotes();
  unregisterGlobals();
  callHook();
?>

