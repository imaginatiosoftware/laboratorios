<?php 
  class Assets { 
    protected $variables = array();
    protected $_path;
    protected $_type;

    function __construct( $path ) {
      $splitted_path = explode( "/", $path );
      $found = false;
      $this->_type = "file";
      
      foreach ( $splitted_path as $part ) {
        if ( $found ) {
          $this->_path .= DS . $part;
        }

        if ( $part == "assets" ) {
          $found = true;
          $this->_path = $part;
        }

        if ( $part == "images" ) {
          $this->_type = "image";
        } 
      }
    }

    function render() {
      if ( file_exists( ROOT . DS . 'app' . DS . $this->_path ) ) {
        if ( $this->_type == "image" ){
          $this->render_image();
        } else {
          include_once ROOT . DS . 'app' . DS . $this->_path;
        }
      } else {
        echo "Non existing asset";
      }
    }

    function render_image() {
      $imgpath = ROOT . DS . 'app' . DS . $this->_path;

      // Get the mimetype for the file
      $finfo = finfo_open(FILEINFO_MIME_TYPE);  // return mime type ala mimetype extension
      $mime_type = finfo_file($finfo, $imgpath);
      finfo_close($finfo);

      switch ($mime_type){
        case "image/jpeg":
          // Set the content type header - in this case image/jpg
          header('Content-Type: image/jpeg');

          // Get image from file
          $img = imagecreatefromjpeg($imgpath);

          // Output the image
          imagejpeg($img);

          break;
        case "image/png":
          // Set the content type header - in this case image/png
          header('Content-Type: image/png');

          // Get image from file
          $img = imagecreatefrompng($imgpath);

          // integer representation of the color black (rgb: 0,0,0)
          $background = imagecolorallocate($img, 0, 0, 0);

          // removing the black from the placeholder
          imagecolortransparent($img, $background);

          // turning off alpha blending (to ensure alpha channel information
          // is preserved, rather than removed (blending with the rest of the
          // image in the form of black))
          imagealphablending($img, false);

          // turning on alpha channel information saving (to ensure the full range
          // of transparency is preserved)
          imagesavealpha($img, true);

          // Output the image
          imagepng($img);

          break;
        case "image/gif":
          // Set the content type header - in this case image/gif
          header('Content-Type: image/gif');

          // Get image from file
          $img = imagecreatefromgif($imgpath);

          // integer representation of the color black (rgb: 0,0,0)
          $background = imagecolorallocate($img, 0, 0, 0);

          // removing the black from the placeholder
          imagecolortransparent($img, $background);

          // Output the image
          imagegif($img);

          break;
      }

      // Free up memory
      imagedestroy($img);
    }
  }
?>
