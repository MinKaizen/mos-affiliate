<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class SponsorLevelShortcode extends AbstractShortcode {

  protected $slug = 'mos_sponsor_level';

  public function shortcode_action( $args ): string {
    $level = User::current()->sponsor()->get_level();
    return $level;
  }

}