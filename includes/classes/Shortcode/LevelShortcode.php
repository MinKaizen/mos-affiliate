<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class LevelShortcode extends AbstractShortcode {

  protected $slug = 'mos_level';

  public function shortcode_action( $args ): string {
    $level = User::current()->get_level();
    return $level;
  }

}