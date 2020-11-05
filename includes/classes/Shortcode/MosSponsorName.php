<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorName extends Shortcode {

  protected $slug = 'mos_sponsor_name';

  public function shortcode_action( $args ): string {
    $sponsor_name = User::current()->sponsor()->get_name();
    return $sponsor_name;
  }

}