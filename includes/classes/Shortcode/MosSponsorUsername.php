<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorUsername extends Shortcode {

  protected $slug = 'mos_sponsor_username';

  public function shortcode_action( $args ): string {
    $sponsor_username = User::current()->sponsor()->get_username();
    return $sponsor_username;
  }

}