<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorFirstName extends Shortcode {

  protected $slug = 'mos_sponsor_first_name';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $sponsor = $user->sponsor();
    $sponsor_first_name = $sponsor->first_name();
    return $sponsor_first_name;
  }

}