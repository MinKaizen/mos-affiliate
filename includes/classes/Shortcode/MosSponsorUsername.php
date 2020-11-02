<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorUsername extends Shortcode {

  protected $slug = 'mos_sponsor_username';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $sponsor = $user->sponsor();
    $sponsor_username = $sponsor->username();
    return $sponsor_username;
  }

}