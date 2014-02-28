<div>
  <?php if ( $flash ) {?>
    <div style="width:100%; background-color: skyblue; color: green;">
      <?php echo $flash['notice']; ?>
    </div>
  <?php }?>

  <h1>Acá hay PUT</h1>

  <?php 
    foreach ( $GLOBALS["_PUT"] as $key => $value ) {
  ?>
      <p><?php echo "$key => $value"; ?></p>
  <?php } ?>
  <br/>
  <a href="/laboratorios/coso/list">Volver</a>
</div>