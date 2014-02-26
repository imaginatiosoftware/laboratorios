<script src="http://code.jquery.com/jquery-2.1.0.min.js"></script>
<script>
  $(document).ready(function(){
    $.get("/laboratorios/get_classes.json",function(data,status){
      console.log(data);
      $.post( "http://mathias.reboot.net.uy/laboratorios/sync/new_db_class", function() {
        alert( "success" );
      });
    });
  });
</script>
<div>
  <p>
  </p>
</div>