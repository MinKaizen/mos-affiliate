<table class='js-campaign-report'>
  <thead>
    <tr>
      <th>Name</th>
      <th>Clicks</th>
      <th>Partners</th>
      <th>Commissions</th>
      <th>EPC</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($campaigns as $campaign): ?>
    <tr>
      <td><?php echo $campaign['name']; ?></td>
      <td><?php echo $campaign['clicks']; ?></td>
      <td><?php echo $campaign['partners']; ?></td>
      <td><?php echo $campaign['commissions']; ?></td>
      <td><?php echo $campaign['EPC']; ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot>
    <tr>
      <td>Name</td>
      <td>Clicks</td>
      <td>Partners</td>
      <td>Commissions</td>
      <td>EPC</td>
    </tr>
  </tfoot>
</table>