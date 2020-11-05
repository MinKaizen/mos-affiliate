<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class NameShortcode extends Shortcode {

  protected $slug = 'mos_name';

  public function shortcode_action( $args ): string {
    $name = User::current()->get_name();
    return $name;
  }

}