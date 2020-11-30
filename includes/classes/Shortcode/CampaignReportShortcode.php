<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use function MOS\Affiliate\get_view;

class CampaignReportShortcode extends AbstractShortcode {

  protected $slug = 'mos_campaign_report';

  public function shortcode_action( $args ): string {
    return get_view( 'campaign_report' );
  }

}