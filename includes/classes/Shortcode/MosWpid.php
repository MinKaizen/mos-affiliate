<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosWpid extends Shortcode {

  protected $slug = 'mos_wpid';

  public function shortcode_action( $args ): string {
    $wpid = User::current()->get_wpid();
    return $wpid;
  }

}