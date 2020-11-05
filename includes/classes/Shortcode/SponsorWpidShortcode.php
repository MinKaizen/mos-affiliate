<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class SponsorWpidShortcode extends Shortcode {

  protected $slug = 'mos_sponsor_wpid';

  public function shortcode_action( $args ): string {
    $sponsor_wpid = User::current()->sponsor()->get_wpid();
    return $sponsor_wpid;
  }

}