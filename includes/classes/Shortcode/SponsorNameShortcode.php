<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class SponsorNameShortcode extends AbstractShortcode {

  protected $slug = 'mos_sponsor_name';

  public function shortcode_action( $args ): string {
    $sponsor_name = User::current()->sponsor()->get_name();
    return $sponsor_name;
  }

}