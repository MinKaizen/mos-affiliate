<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class WpidShortcode extends AbstractShortcode {

  protected $slug = 'mos_wpid';

  public function shortcode_action( $args ): string {
    $wpid = User::current()->get_wpid();
    return $wpid;
  }

}