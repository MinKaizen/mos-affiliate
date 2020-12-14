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
        <td><?php echo $referral['date']; ?></td>
        <td><?php echo $referral['username']; ?></td>
        <td><?php echo $referral['name']; ?></td>
        <td><?php echo $referral['email']; ?></td>
        <td><?php echo $referral['level']; ?></td>
        <td><?php echo $referral['progress']; ?></td>
        <td><?php echo $referral['campaign']; ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
  <tr>
      <td>Date</td>
      <td>Username</td>
      <td>Name</td>
      <td>Email</td>
      <td>Level</td>
      <td>Progress</td>
      <td>Campaign</td>
    </tr>
  </tfoot>
</table>