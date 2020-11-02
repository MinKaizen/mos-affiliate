<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorLastName extends Shortcode {

  protected $slug = 'mos_sponsor_last_name';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $sponsor = $user->sponsor();
    $sponsor_last_name = $sponsor->last_name();
    return $sponsor_last_name;
  }

}