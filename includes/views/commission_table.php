<?php namespace MOS\Affiliate; ?>
<table>
  <thead class="table-header">
    <tr class="table-header__row">
      <th class="table-cell table-header-cell col-date">Date</th>
      <th class="table-cell table-header-cell col-amount">Amount</th>
      <th class="table-cell table-header-cell col-name">Name</th>
      <th class="table-cell table-header-cell col-email">Email</th>
      <th class="table-cell table-header-cell col-product">Product</th>
      <th class="table-cell table-header-cell col-campaign">Campaign</th>
      <th class="table-cell table-header-cell col-payment">Payment</th>
    </tr>
  </thead>
  <tbody class="table-body">
    <?php foreach( $rows as $row ): ?>
    <tr class="table-body__row <?php if($row['amount']) ?>">
      <td class="table-cell table-body-cell col-date"><?php echo $row['date']; ?></td>
      <td class="table-cell table-body-cell col-amount"><?php echo format_currency( (float) $row['amount'], 0 ); ?></td>
      <td class="table-cell table-body-cell col-name"><?php echo ucwords( $row['display_name'] ); ?></td>
      <td class="table-cell table-body-cell col-email"><?php echo strtolower( $row['user_email'] ); ?></td>
      <td class="table-cell table-body-cell col-product"><?php echo $row['description']; ?></td>
      <td class="table-cell table-body-cell col-campaign"><?php echo $row['campaign']; ?></td>
      <td class="table-cell table-body-cell col-payment">
        <?php echo $row['payout_method']; ?>
        <span class="tooltip" style="display: none;" >
          <p>Date: <?php echo $row['payout_date'] ?></p>
          <p>Method: <?php echo $row['payout_method'] ?></p>
          <p>Address: <?php echo $row['payout_address'] ?></p>
          <p>Transaction ID: <?php echo $row['payout_transaction_id'] ?></p>
        </span>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot class="table-footer">
    <tr class="table-footer__row">
      <th class="table-cell table-footer-cell col-date">Date</th>
      <th class="table-cell table-footer-cell col-amount">Amount</th>
      <th class="table-cell table-footer-cell col-name">Name</th>
      <th class="table-cell table-footer-cell col-email">Email</th>
      <th class="table-cell table-footer-cell col-product">Product</th>
      <th class="table-cell table-footer-cell col-campaign">Campaign</th>
      <th class="table-cell table-footer-cell col-payment">Payment</th>
    </tr>
  </tfoot>
</table>