<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class SponsorUsernameShortcode extends AbstractShortcode {

  protected $slug = 'mos_sponsor_username';

  public function shortcode_action( $args ): string {
    $sponsor_username = User::current()->sponsor()->get_username();
    return $sponsor_username;
  }

}