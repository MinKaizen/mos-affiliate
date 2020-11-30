<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class MisShortcode extends AbstractShortcode {

  protected $slug = 'mos_mis';

  protected $defaults = [
    'network' => '',
  ];

  public function shortcode_action( $args ): string {
    $mis = User::current()->get_mis( $args['network'] );
    return $mis;
  }

}