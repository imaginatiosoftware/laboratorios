<?php 
  class Assets { 
  	function include_asset( $route ) {
  		if ( file_exists( ROOT . DS . 'app' . DS . 'assets' . DS . $route ) ) {
  			include_once ROOT . DS . 'app' . DS . 'assets' . DS . $route;
  		}
  	}
  }
?>