<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class UsernameShortcode extends AbstractShortcode {

  protected $slug = 'mos_username';

  public function shortcode_action( $args ): string {
    $username = User::current()->get_username();    
    return $username;
  }

}