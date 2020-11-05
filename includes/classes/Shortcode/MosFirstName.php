<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosFirstName extends Shortcode {

  protected $slug = 'mos_first_name';

  public function shortcode_action( $args ): string {
    $first_name = User::current()->get_first_name();    
    return $first_name;
  }

}