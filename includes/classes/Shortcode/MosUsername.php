<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosUsername extends Shortcode {

  protected $slug = 'mos_username';

  public function shortcode_action( $args ): string {
    $username = User::current()->get_username();    
    return $username;
  }

}