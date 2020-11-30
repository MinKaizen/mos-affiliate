<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class SponsorAffidShortcode extends AbstractShortcode {

  protected $slug = 'mos_sponsor_affid';

  public function shortcode_action( $args ): string {
    $sponsor_affid = (string) User::current()->sponsor()->get_affid();
    return $sponsor_affid;
  }

}