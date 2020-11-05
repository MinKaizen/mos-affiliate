<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class SponsorAffidShortcode extends Shortcode {

  protected $slug = 'mos_sponsor_affid';

  public function shortcode_action( $args ): string {
    $sponsor_affid = User::current()->sponsor()->get_affid();
    return $sponsor_affid;
  }

}