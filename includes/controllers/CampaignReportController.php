<?php

class CampaignReportController extends MosAffiliateController {

  protected $variables = [
    'campaigns',
    'headers',
  ];


  protected function campaigns() {
    $db = MosAffiliateDb::instance();
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