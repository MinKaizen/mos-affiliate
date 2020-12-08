<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class CampaignShortcode extends AbstractShortcode {

  protected $slug = 'mos_campaign';

  public function shortcode_action( $args ): string {
    $campaign = (string) User::current()->get_campaign();
    return $campaign;
  }

}