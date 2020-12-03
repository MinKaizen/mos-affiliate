<?php declare(strict_types=1);

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

    if ( ! $sponsor->exists() ) {
      return Mis::default_value_for( $args['network'] );
    }
    
    if ( ! $sponsor->qualifies_for_mis( $args['network'] ) ) {
      return Mis::default_value_for( $args['network'] );
    }

    $mis = $sponsor->get_mis( $args['network'] );

    if ( empty( $mis ) ) {
      return Mis::default_value_for( $args['network'] );
    }

    return $mis;
  }


}