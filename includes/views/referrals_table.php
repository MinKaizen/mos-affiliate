<table class='js-referrals-table'>
  <thead>
    <tr>
      <th>Date</th>
      <th>Username</th>
      <th>Name</th>
      <th>Email</th>
      <th>Level</th>
      <th>Progress</th>
      <th>Campaign</th>
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