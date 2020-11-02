<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosLastName extends Shortcode {

  protected $slug = 'mos_last_name';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $last_name = $user->last_name;    
    return $last_name;
  }

}