<?php declare(strict_types=1);

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;
use MOS\Affiliate\Database;

class CampaignReport extends Controller {

  private $campaigns = [];


  public function __construct() {
    $db = new Database();
    $campaigns = $db->get_campaign_data();

    // Add empty partners column to campaigns
    foreach ( $campaigns as &$campaign ) {
      $campaign['partners'] = 0;
    }
    
    // Get a list of referrals
    $referrals = $db->get_referrals(['level', 'campaign']);

    // Count partners
    foreach ( $referrals as $referral ) {
      if ( strpos( 'partner', $referral['level']) !== false ) {
        $campaigns[$referral['campaign']]['partners']++;
      }
    }

    $this->campaigns = $campaigns;
  }


  protected function export_campaigns(): array {
    return $this->campaigns;
  }


  protected function export_headers(): array {
    if ( empty( $this->campaigns ) ) {
      return [];
    }
    $first_element = reset( $this->campaigns );
    return array_keys( $first_element );
  }


}