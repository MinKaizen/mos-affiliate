<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class NameShortcode extends AbstractShortcode {

  protected $slug = 'mos_name';

  public function shortcode_action( $args ): string {
    $name = User::current()->get_name();
    return $name;
  }

}