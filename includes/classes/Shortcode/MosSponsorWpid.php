<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorWpid extends Shortcode {

  protected $slug = 'mos_sponsor_wpid';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $sponsor = $user->sponsor();
    $sponsor_wpid = $sponsor->get_wpid();
    return $sponsor_wpid;
  }

}