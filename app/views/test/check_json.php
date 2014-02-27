<script src="http://code.jquery.com/jquery-2.1.0.min.js"></script>
<script>
  $(document).ready(function(){
    $.get("/laboratorios/get_classes.json",function(data,status){
      console.log(data);
      //la ruta esta bien tendrias que probarlo vos para guardar tus classes en mi base
      //no tengo ninguna ahora
      // se entiende??????????
      // para, un foreach de esta lista data hay que hacer y un post por cada clase
      //que no entendiste?
      $.post( "http://mathias.reboot.net.uy/laboratorios/sync/new_db_class", function() {
        alert( "success" );
      });
      //vos decís de ir creando clases ahora? así como al vuelo?
    });
  });
</script>
<div>
  <p>
  </p>
</div>