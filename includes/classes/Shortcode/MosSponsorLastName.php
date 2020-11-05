<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorLastName extends Shortcode {

  protected $slug = 'mos_sponsor_last_name';

  public function shortcode_action( $args ): string {
    $sponsor_last_name = User::current()->sponsor()->get_last_name();
    return $sponsor_last_name;
  }

}