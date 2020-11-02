<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosUsername extends Shortcode {

  protected $slug = 'mos_username';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $username = $user->user_login;    
    return $username;
  }

}