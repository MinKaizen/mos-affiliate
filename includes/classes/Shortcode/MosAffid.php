<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class MosAffid extends Shortcode {

  protected $slug = 'mos_affid';

  public function shortcode_action( $args ): string {
    $affid = User::current()->get_affid();
    return $affid;
  }

}