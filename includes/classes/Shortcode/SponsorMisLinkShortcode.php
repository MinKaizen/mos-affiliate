<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;
use function MOS\Affiliate\mis_generate_link;
use function MOS\Affiliate\mis_default_value;

class SponsorMisLinkShortcode extends AbstractShortcode {

  protected $slug = 'mos_sponsor_mis_link';

  protected $defaults = [
    'network' => '',
  ];

  public function shortcode_action( $args ): string {
    $sponsor = User::current()->sponsor();
    $value = $sponsor->get_mis( $args['network'] );

    if ( !$sponsor->exists() ) {
      $value = mis_default_value( $args['network'] );
    } elseif ( !$sponsor->qualifies_for_mis( $args['network'] ) ) {
      $value = mis_default_value( $args['network'] );
    } elseif ( empty( $value ) ) {
      $value = mis_default_value( $args['network'] );
    }

    $link = mis_generate_link( $args['network'], $value );
    return $link;
  }

}
