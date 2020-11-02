<?php

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;
use MOS\Affiliate\Database;

class CampaignReport extends Controller {

  protected $variables = [
    'campaigns',
    'headers',
  ];


  protected function campaigns() {
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

    return $campaigns;
  }


}