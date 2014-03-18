<html>
<head>
  <title><?php echo $title?></title>
  <link rel="stylesheet" href="<?php echo 'assets' . DS . 'css' . DS . 'main.css.php'; ?>" media="screen">
</head>
<body>
<div>
  <?php if ( isset( $flash ) ) { ?>
    <?php if( isset( $flash['error'] ) ) { ?>
      <div class='flash error'>
        <?php echo $flash['error']; ?>
      </div>
    <?php } ?>

    <?php if( isset( $flash['success'] ) ) { ?>
      <div class='flash success'>
        <?php echo $flash['success']; ?>
      </div>
    <?php } ?>

    <?php if( isset( $flash['notice'] ) ) { ?>
      <div class='flash notice'>
        <?php echo $flash['notice']; ?>
      </div>
    <?php } ?>
  <?php } ?>
</div>
<div class="wrapper">
<h1>Imaginatio's MVC. Default HEADER</h1>