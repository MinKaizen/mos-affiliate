<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use function MOS\Affiliate\get_view;

class ReferralsTableShortcode extends AbstractShortcode {

  protected $slug = 'mos_referrals_table';

  public function shortcode_action( $args ): string {
    return get_view( 'referrals_table' );
  }

}