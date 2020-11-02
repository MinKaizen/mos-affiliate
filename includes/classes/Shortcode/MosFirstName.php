<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosFirstName extends Shortcode {

  protected $slug = 'mos_first_name';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $first_name = $user->first_name;    
    return $first_name;
  }

}