<div>
  <?php if ( isset($flash) ) {?>
    <div style="background-color: skyblue; color: green; width: 100%; text-align: center;">
      <?php echo $flash['notice']; ?>
    </div>
  <?php }?>
  <h3><?php echo $cosovo; ?></h3>

  <h4><?php echo $coso->_attributes['nombre_coso']; ?></h4>

  <form method="post" action="/laboratorios/coso/list">
    <input type="hidden" name="REQUEST_METHOD" value="patch"/>
    <input type="text" id="idparam" name="nameparam"/>
    <input type="submit" value="Enviar"/>
  </form>

  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
  <script type="text/javascript">
    $.ajax({
      url: "/laboratorios/coso/list",
      type: "patch",
      data: { "dato1": "valorDato1", "dato2": "valorDato2" }
    }).done(function( data ) {
      console.log( data );
    });
  </script>
</div>