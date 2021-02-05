<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

use function \MOS\Affiliate\mis_default_value;

class SponsorMisShortcode extends AbstractShortcode {

  protected $slug = 'mos_sponsor_mis';

  protected $defaults = [
    'network' => '',
  ];


  public function shortcode_action( $args ): string {
    $sponsor = User::current()->sponsor();

    if ( ! $sponsor->exists() ) {
      return mis_default_value( $args['network'] );
    }
    
    if ( ! $sponsor->qualifies_for_mis( $args['network'] ) ) {
      return mis_default_value( $args['network'] );
    }

    $mis = $sponsor->get_mis( $args['network'] );

    if ( empty( $mis ) ) {
      return mis_default_value( $args['network'] );
    }

    return $mis;
  }


}