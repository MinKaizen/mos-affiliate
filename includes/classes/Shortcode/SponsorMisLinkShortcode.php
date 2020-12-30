<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class SponsorMisLinkShortcode extends AbstractShortcode {

  protected $slug = 'mos_sponsor_mis_link';

  protected $defaults = [
    'network' => '',
  ];

  public function shortcode_action( $args ): string {
    $link = User::current()->sponsor()->get_mis_link( $args['network'] );
    return $link;
  }

}
