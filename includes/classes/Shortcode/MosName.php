<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosName extends Shortcode {

  protected $slug = 'mos_name';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $name = $user->name;
    return $name;
  }

}