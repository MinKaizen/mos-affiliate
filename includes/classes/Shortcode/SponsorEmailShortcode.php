<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class SponsorEmailShortcode extends AbstractShortcode {

  protected $slug = 'mos_sponsor_email';

  public function shortcode_action( $args ): string {
    $sponsor_email = User::current()->sponsor()->get_email();
    return $sponsor_email;
  }

}