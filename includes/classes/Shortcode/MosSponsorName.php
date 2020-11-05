<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorName extends Shortcode {

  protected $slug = 'mos_sponsor_name';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $sponsor = $user->sponsor();
    $sponsor_name = $sponsor->get_name();
    return $sponsor_name;
  }

}