<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosWpid extends Shortcode {

  protected $slug = 'mos_wpid';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $wpid = $user->wpid();
    return $wpid;
  }

}