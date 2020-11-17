<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use function MOS\Affiliate\get_view;

class CampaignListShortcode extends AbstractShortcode {

  protected $slug = 'mos_campaign_list';

  public function shortcode_action( $args ): string {
    return get_view( 'campaign_list' );
  }

}