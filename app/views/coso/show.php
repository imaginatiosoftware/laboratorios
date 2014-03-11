<div>
  <div>
    <span>ID: <?php echo $coso->id; ?></span>
  </div>
  <div>
    <span>Descripci&oacute;n: <?php echo $coso->descripcion_coso; ?></span>
  </div>

  <ul>
    <?php foreach ( $coso->ruedas as $rueda ) {?>
      <li>
        <a href="/laboratorios/rueda/show/<?php $rueda->id ?>">
          <?php echo $rueda->id; ?> - <?php echo $rueda->descripcion; ?>
        </a>
      </li>
    <?php } ?>
  </ul>

  <div>
    <a href="/laboratorios/coso/list">Volver</a>
  </div>
</div>