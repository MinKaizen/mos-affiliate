<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosLevel extends Shortcode {

  protected $slug = 'mos_level';

  public function shortcode_action( $args ): string {
    $level = User::current()->get_level();
    return $level;
  }

}