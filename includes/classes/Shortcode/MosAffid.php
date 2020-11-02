<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosAffid extends Shortcode {

  protected $slug = 'mos_affid';

  public function shortcode_action( $args ): string {
    $user = new User( \get_current_user_id() );
    return print_r( $user->affid(), true );
  }

}