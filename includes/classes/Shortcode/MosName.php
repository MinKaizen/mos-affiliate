<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosName extends Shortcode {

  protected $slug = 'mos_name';

  public function shortcode_action( $args ): string {
    $user = User::current();

    if ($user->first_name && $user->last_name) {
      $name = implode( ' ', [$user->first_name, $user->last_name] );
    } elseif ( $user->first_name ) {
      $name = $user->first_name;
    } else {
      $name = $user->display_name;
    }
    
    return $name;
  }

}