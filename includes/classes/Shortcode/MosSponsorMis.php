<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;
use MOS\Affiliate\Mis;

class MosSponsorMis extends Shortcode {

  protected $slug = 'mos_sponsor_mis';

  protected $defaults = [
    'network' => '',
  ];


  public function shortcode_action( $args ): string {
    $sponsor = User::current()->sponsor();

    if ( $sponsor->is_empty() ) {
      return $this->get_default_value( $args['network'] );
    }
    
    if ( ! $sponsor->qualifies_for_mis( $args['network'] ) ) {
      return $this->get_default_value( $args['network'] );
    }

    $mis = $sponsor->get_mis( $args['network'] );

    if ( empty( $mis ) ) {
      return $this->get_default_value( $args['network'] );
    }

    return $mis;
  }


  private function get_default_value( string $slug ): string {
    $mis = Mis::get( $slug );
    $default_value = $mis->exists() ? $mis->default : '';
    return $default_value;
  }


}