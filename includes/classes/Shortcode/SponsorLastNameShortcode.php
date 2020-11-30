<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class SponsorLastNameShortcode extends AbstractShortcode {

  protected $slug = 'mos_sponsor_last_name';

  public function shortcode_action( $args ): string {
    $sponsor_last_name = User::current()->sponsor()->get_last_name();
    return $sponsor_last_name;
  }

}