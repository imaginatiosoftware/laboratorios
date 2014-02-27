<div>
  <?php if ( isset($flash) ) {?>
    <div style="background-color: skyblue; color: green; width: 100%; text-align: center;">
      <?php echo $flash['notice']; ?>
    </div>
  <?php }?>
  <h3><?php echo $cosovo; ?></h3>

  <form method="delete" action="/laboratorios/coso/list">
    <input type="text" id="idparam" name="nameparam"/>
    <input type="submit" value="Enviar"/>
  </form>
</div>