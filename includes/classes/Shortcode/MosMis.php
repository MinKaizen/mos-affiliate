<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosMis extends Shortcode {

  protected $slug = 'mos_mis';

  protected $defaults = [
    'network' => '',
  ];

  public function shortcode_action( $args ): string {
    $mis = User::current()->get_mis( $args['network'] );
    return $mis;
  }

}