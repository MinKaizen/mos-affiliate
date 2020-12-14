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
        <?php foreach( $campaign as $value ): ?>
        <td><?php echo $value; ?></td>
        <?php endforeach; ?>
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