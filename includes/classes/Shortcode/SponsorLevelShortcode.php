<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class SponsorLevelShortcode extends Shortcode {

  protected $slug = 'mos_sponsor_level';

  public function shortcode_action( $args ): string {
    $level = User::current()->sponsor()->get_level();
    return $level;
  }

}