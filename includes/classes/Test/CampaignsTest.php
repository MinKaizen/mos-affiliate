<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;

class CampaignsTest extends Test {


  public function test_controller(): void {
    $empty_campaign = '(none)';
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
    
    // Empty Campaign: 2 yearly_partner, 2 monthly
    $this->user_give_access( $empty_campaign_referral1->ID, 'yearly_partner' );
    $this->user_give_access( $empty_campaign_referral2->ID, 'yearly_partner' );

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
    
    // Bloody campaign: 2 coaching, 2, lifetime, 2 yearly, 2 monthly
    $this->user_give_access( $bloody_campaign_referral1->ID, 'coaching' );
    $this->user_give_access( $bloody_campaign_referral2->ID, 'coaching' );

    // Bloody campaign: $32 commissions
    $this->create_test_commission( ['amount' => 10, 'campaign' => 'bloody_campaign', 'earner_id' => $this->_injected_user->ID] );
    $this->create_test_commission( ['amount' => 8, 'campaign' => 'bloody_campaign', 'earner_id' => $this->_injected_user->ID] );
    $this->create_test_commission( ['amount' => 6, 'campaign' => 'bloody_campaign', 'earner_id' => $this->_injected_user->ID] );
    $this->create_test_commission( ['amount' => 8, 'campaign' => 'bloody_campaign', 'earner_id' => $this->_injected_user->ID] );

    $actual_campaigns = apply_filters( 'mos_campaigns', [] );

    $expected_campaigns = [
      $empty_campaign => [
        'name' => $empty_campaign,
        'clicks' => 5,
        'referrals' => 3,
        'monthly_partner' => 2,
        'yearly_partner' => 2,
        'lifetime_partner' => 0,
        'coaching' => 0,
        'commissions' => 50,
        'epc' => 10.0,
        'epc_formatted' => '$10.00',
        'commissions_formatted' => '$50',
      ],
      'bloody_campaign' => [
        'name' => 'bloody_campaign',
        'clicks' => 4,
        'referrals' => 4,
        'monthly_partner' => 2,
        'yearly_partner' => 2,
        'lifetime_partner' => 2,
        'coaching' => 2,
        'commissions' => 32,
        'epc' => 8.0,
        'epc_formatted' => '$8.00',
        'commissions_formatted' => '$32',
      ],
    ];

    // var_dump( $bloody_campaign_referral1->get_access_list() );
    // var_dump( $bloody_campaign_referral2->get_access_list() );
    // var_dump( $bloody_campaign_referral3->get_access_list() );
    // var_dump( $bloody_campaign_referral4->get_access_list() );
    
    $this->assert_equal( $expected_campaigns, $actual_campaigns );
  }

}