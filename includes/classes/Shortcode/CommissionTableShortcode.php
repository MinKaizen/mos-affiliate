<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\HtmlTable;

use function MOS\Affiliate\get_view;

class CommissionTableShortcode extends AbstractShortcode {

  protected $slug = 'mos_commission_table';

  public function shortcode_action( $args ): string {
    return get_view( 'commission_table' );
  }

}