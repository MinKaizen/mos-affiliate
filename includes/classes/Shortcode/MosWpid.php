<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;

class MosWpid extends Shortcode {

  protected $slug = 'mos_wpid';

  public function shortcode_action( $args ): string {
    return \get_current_user_id();
  }

}