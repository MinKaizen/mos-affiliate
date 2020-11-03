<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosSponsorMis extends Shortcode {

  protected $slug = 'mos_sponsor_mis';

  protected $defaults = [
    'network' => '',
  ];

  public function shortcode_action( $args ): string {
    if ( $args['network'] == '' ) {
      return '';
    }

    $mis = User::current()->sponsor()->mis( $args['network'] );

    return $mis;
  }

}