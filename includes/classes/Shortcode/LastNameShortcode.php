<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class LastNameShortcode extends AbstractShortcode {

  protected $slug = 'mos_last_name';

  public function shortcode_action( $args ): string {
    $last_name = User::current()->get_last_name();    
    return $last_name;
  }

}