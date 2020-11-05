<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class SponsorFirstNameShortcode extends Shortcode {

  protected $slug = 'mos_sponsor_first_name';

  public function shortcode_action( $args ): string {
    $sponsor_first_name = User::current()->sponsor()->get_first_name();
    return $sponsor_first_name;
  }

}