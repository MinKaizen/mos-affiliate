<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use function MOS\Affiliate\get_view;

class MosCampaignList extends Shortcode {

  protected $slug = 'mos_campaign_list';

  public function shortcode_action( $args ): string {
    return get_view( 'campaign_list' );
  }

}