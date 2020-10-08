<table>
  <thead>
    <tr>
      <?php foreach( $headers as $header ):?>
      <th><?php echo $header; ?></th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach($campaigns as $campaign): ?>
      <tr>
        <?php foreach( $campaign as $value ): ?>
        <td><?php echo $value; ?></td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<p>
  <?php
    $db = new MosAffiliateDb();
    $referrals = $db->get_referrals([
      'id',
      'username',
      'email',
      'first_name',
      'last_name',
      'affid',
      'level',
      'date',
    ]);

    echo $referrals;
    echo "<br>";
    var_dump( $referrals );
  ?>
</p>