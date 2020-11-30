<table class='js-campaign-report'>
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