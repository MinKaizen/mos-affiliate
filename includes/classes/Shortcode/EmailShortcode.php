<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class EmailShortcode extends AbstractShortcode {

  protected $slug = 'mos_email';

  public function shortcode_action( $args ): string {
    $email = User::current()->get_email();    
    return $email;
  }

}