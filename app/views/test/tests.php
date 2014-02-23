<div>
  <table>
    <tr>
      <th>Id</th>
      <th>Test Name</th>
    </tr>
    <?php foreach ( $tests as $test ) { ?>
      <tr>
        <td><?php echo $test->id; ?></td>
        <td><?php echo $test->test_name; ?></td>
      </tr>
    <?php } ?>
  </table>
</div>