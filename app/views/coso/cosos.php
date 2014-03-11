<div>
  <h2>Lista de cosos:</h2>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Descripci&oacute;n</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ( $cosos as $coso ) {?>
        <tr>
          <td><?php echo $coso->id; ?></td>
          <td><?php echo $coso->descripcion_coso ?></td>
          <td><a href="/laboratorios/coso/show/<?php echo $coso->id; ?>">show</a></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>