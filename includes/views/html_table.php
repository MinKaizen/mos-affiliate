<?php use function \MOS\Affiliate\proper_to_kebab_case; ?>

<table id="<?php echo $table->id(); ?>" class="table <?php echo $table->class(); ?>">
  <thead class="table-header">
    <tr class="table-header__row">
      <?php foreach( $table->headers() as $header ): ?>
      <th class="table-cell table-header-cell col-<?php echo proper_to_kebab_case($header); ?>"><?php echo $header; ?></th>
      <?php endforeach; ?>
    </tr>
  </thead>
  <tbody class="table-body">
    <?php foreach( $table->rows() as $row ): ?>
    <tr class="table-body__row">
      <?php foreach( $row as $cell_index => $cell ): ?>
      <td class="table-cell table-body-cell col-<?php echo proper_to_kebab_case($cell_index); ?>"><?php echo $cell; ?></td>
      <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
  </tbody>
  <tfoot class="table-footer">
    <tr class="table-footer__row">
      <?php foreach( $table->headers() as $header ): ?>
      <th class="table-cell table-footer-cell col-<?php echo proper_to_kebab_case($header); ?>"><?php echo $header; ?></th>
      <?php endforeach; ?>
    </tr>
  </tfoot>
</table>
