<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorWpid extends Shortcode {

  protected $slug = 'mos_sponsor_wpid';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $sponsor = $user->get_sponsor();
    $sponsor_wpid = $sponsor->ID;
    return $sponsor_wpid;
  }

}