<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorEmail extends Shortcode {

  protected $slug = 'mos_sponsor_email';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $sponsor = $user->sponsor();
    $sponsor_name = $sponsor->email();
    return $sponsor_name;
  }

}