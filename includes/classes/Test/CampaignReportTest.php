<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\Controller\CampaignReport;

class CampaignReportTest extends Test {


  public function test_controller(): void {
    $empty_campaign = CampaignReport::EMPTY_CAMPAIGN_NAME;
    $this->_injected_user = $this->create_test_user();
    $this->set_user();

    // Empty campaign: 5 clicks
    $this->create_test_click( $this->_injected_user->ID );
    $this->create_test_click( $this->_injected_user->ID );
    $this->create_test_click( $this->_injected_user->ID );
    $this->create_test_click( $this->_injected_user->ID );
    $this->create_test_click( $this->_injected_user->ID );
    
    // Empty Campaign: 3 referrals
    $empty_campaign_referral1 = $this->create_test_user();
    $empty_campaign_referral2 = $this->create_test_user();
    $empty_campaign_referral3 = $this->create_test_user();
    $this->create_test_referral( $empty_campaign_referral1->ID, $this->_injected_user->ID );
    $this->create_test_referral( $empty_campaign_referral2->ID, $this->_injected_user->ID );
    $this->create_test_referral( $empty_campaign_referral3->ID, $this->_injected_user->ID );
    
    // Empty Campaign: 1 partner
    $empty_campaign_referral1->set_role( 'monthly_partner' );

    // Empty campaign: $50 commissions
    $this->create_test_commission( ['amount' => 20, 'earner_id' => $this->_injected_user->ID] );
    $this->create_test_commission( ['amount' => 30, 'earner_id' => $this->_injected_user->ID] );
    
    // Bloody campaign: 4 clicks
    $this->create_test_click( $this->_injected_user->ID, 'bloody_campaign' );
    $this->create_test_click( $this->_injected_user->ID, 'bloody_campaign' );
    $this->create_test_click( $this->_injected_user->ID, 'bloody_campaign' );
    $this->create_test_click( $this->_injected_user->ID, 'bloody_campaign' );

    // Bloody campaign: 4 referrals
    $bloody_campaign_referral1 = $this->create_test_user();
    $bloody_campaign_referral2 = $this->create_test_user();
    $bloody_campaign_referral3 = $this->create_test_user();
    $bloody_campaign_referral4 = $this->create_test_user();
    $this->create_test_referral( $bloody_campaign_referral1->ID, $this->_injected_user->ID, 'bloody_campaign' );
    $this->create_test_referral( $bloody_campaign_referral2->ID, $this->_injected_user->ID, 'bloody_campaign' );
    $this->create_test_referral( $bloody_campaign_referral3->ID, $this->_injected_user->ID, 'bloody_campaign' );
    $this->create_test_referral( $bloody_campaign_referral4->ID, $this->_injected_user->ID, 'bloody_campaign' );
    
    // Bloody campaign: 2 partners
    $bloody_campaign_referral1->set_role( 'yearly_partner' );
    $bloody_campaign_referral2->set_role( 'monthly_partner' );

    // Bloody campaign: $32 commissions
    $this->create_test_commission( ['amount' => 10, 'campaign' => 'bloody_campaign', 'earner_id' => $this->_injected_user->ID] );
    $this->create_test_commission( ['amount' => 8, 'campaign' => 'bloody_campaign', 'earner_id' => $this->_injected_user->ID] );
    $this->create_test_commission( ['amount' => 6, 'campaign' => 'bloody_campaign', 'earner_id' => $this->_injected_user->ID] );
    $this->create_test_commission( ['amount' => 8, 'campaign' => 'bloody_campaign', 'earner_id' => $this->_injected_user->ID] );

    $controller = new CampaignReport();
    $vars = $controller->get_vars();
    $actual_campaigns = $vars['campaigns'];

    $expected_campaigns = [
      $empty_campaign => [
        'name' => $empty_campaign,
        'clicks' => '5', // note: string
        'referrals' => 3,
        'partners' => 1,
        'commissions' => '$50',
        'EPC' => '$10.00',
      ],
      'bloody_campaign' => [
        'name' => 'bloody_campaign',
        'clicks' => '4', // note: string
        'referrals' => 4,
        'partners' => 2,
        'commissions' => '$32',
        'EPC' => '$8.00',
      ],
    ];
    
    $this->assert_equal( $expected_campaigns, $actual_campaigns );
  }

}