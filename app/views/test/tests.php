<div>
  <div style="background-color: red; color: white; width: 100%;">
    <?php if ( isset($flash) ) { ?>
      <p><?php echo $flash; ?></p>
    <?php }?>
  </div>

  <table>
    <thead>
      <tr>
        <th>Id</th>
        <th>Test Name</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ( $tests as $test ) { ?>
        <tr>
          <td><?php echo $test->id; ?></td>
          <td><?php echo $test->test_name; ?></td>
          <td><a href="/laboratorios/test/show/<?php echo $test->id; ?>">show</a></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>