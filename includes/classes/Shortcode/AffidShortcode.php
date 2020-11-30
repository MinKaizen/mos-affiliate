<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class AffidShortcode extends AbstractShortcode {

  protected $slug = 'mos_affid';

  public function shortcode_action( $args ): string {
    $affid = (string) User::current()->get_affid();
    return $affid;
  }

}