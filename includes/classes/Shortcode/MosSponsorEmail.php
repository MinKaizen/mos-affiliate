<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorEmail extends Shortcode {

  protected $slug = 'mos_sponsor_email';

  public function shortcode_action( $args ): string {
    $sponsor_email = User::current()->sponsor()->get_email();
    return $sponsor_email;
  }

}