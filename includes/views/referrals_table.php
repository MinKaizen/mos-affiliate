<table class='js-referrals-table'>
  <thead>
    <tr>
      <?php foreach( $headers as $header ):?>
      <th><?php echo $header; ?></th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody>
    <?php foreach($referrals as $referral): ?>
      <tr>
        <?php foreach( $referral as $value ): ?>
        <td><?php echo $value; ?></td>
        <?php endforeach; ?>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>