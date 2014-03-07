<?php
  class SyncController extends Controller {
    function get_all_db_classes(){
      /** Para no renderizar una vista */
      $this->not_render();
      $classes_from_database = new DBClasses();
      $classes_from_database->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      $classes_from_db = $classes_from_database->import_all_db_classes();
      $this->render_json($classes_from_db);
    }

    function new_db_class( $params ){
      /** Para no renderizar una vista */
      $this->not_render();

      foreach($params as $new_class){ 
        $classes_from_database = new DBClasses();
        $classes_from_database->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $classes_from_database->new_db_clas($new_class["code"],$new_class["type"],$new_class["class_name"]);
      }
    }
  }
?>  