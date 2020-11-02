<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorAffid extends Shortcode {

  protected $slug = 'mos_sponsor_affid';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $sponsor = $user->sponsor();
    $sponsor_affid = $sponsor->affid();
    return $sponsor_affid;
  }

}