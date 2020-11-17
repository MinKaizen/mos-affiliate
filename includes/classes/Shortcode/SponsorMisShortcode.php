<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;
use MOS\Affiliate\Mis;

class SponsorMisShortcode extends AbstractShortcode {

  protected $slug = 'mos_sponsor_mis';

  protected $defaults = [
    'network' => '',
  ];


  public function shortcode_action( $args ): string {
    $sponsor = User::current()->sponsor();

    if ( $sponsor->is_empty() ) {
      return Mis::get_default( $args['network'] );
    }
    
    if ( ! $sponsor->qualifies_for_mis( $args['network'] ) ) {
      return Mis::get_default( $args['network'] );
    }

    $mis = $sponsor->get_mis( $args['network'] );

    if ( empty( $mis ) ) {
      return Mis::get_default( $args['network'] );
    }

    return $mis;
  }


}