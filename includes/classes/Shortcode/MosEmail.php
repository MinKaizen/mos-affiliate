<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosEmail extends Shortcode {

  protected $slug = 'mos_email';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $email = $user->get_email();    
    return $email;
  }

}