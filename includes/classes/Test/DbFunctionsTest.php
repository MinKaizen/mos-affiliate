<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;
use \MOS\Affiliate\User;

use function \MOS\Affiliate\ranstr;

class DbFunctionsTest extends Test {


  public function test_create_user(): void {
    $user_data = [
      'user_email' => ranstr() . "@test123.testingla",
      'user_login' => ranstr(),
      'first_name' => ranstr(),
      'last_name' => ranstr(),
    ];
    $user = $this->create_test_user( $user_data );

    $this->assert_user_id_exists( $user->ID, "User ID should exist after create" );
    $this->assert_instanceof( $user, '\MOS\Affiliate\User' );
    $this->assert_equal( $user->get( Test::TEST_META_KEY ), Test::TEST_META_VALUE, 'User created via test should have test meta' );
    $this->assert_user_is_affiliate( $user->ID, "User should get an affid after create" );
    $this->assert_equal( $user->get('first_name'), $user_data['first_name'], "Newly created user first_name should match user_data array" );
    $this->assert_equal( $user->get('last_name'), $user_data['last_name'], "Newly created user last_name should match user_data array" );
    $this->assert_equal( $user->get('email'), $user_data['email'], "Newly created user email should match user_data array" );
    $this->assert_equal( $user->get('user_login'), $user_data['user_login'], "Newly created user user_login should match user_data array" );
    
    // Delete user
    $this->delete_test_users();
    $this->assert_user_id_not_exists( $user->ID, "User ID should not exist after delete" );
    $this->assert_user_is_not_affiliate( $user->ID, "User affid should not exist after delete" );
    $this->assert_false( \get_user_meta( $user->ID, 'first_name' ), "User meta should be deleted after user delete: first_name" );
    $this->assert_false( \get_user_meta( $user->ID, 'last_name' ), "User meta should be deleted after user delete: last_name" );
    $this->assert_false( \get_user_meta( $user->ID, Test::TEST_META_KEY ), "User meta should be deleted after user delete: " . Test::TEST_META_KEY );
  }


  public function test_create_empty_user(): void {
    $user = $this->create_test_user();
    $this->assert_user_id_exists( $user->ID, "User ID should exist after create" );
    $this->assert_instanceof( $user, '\MOS\Affiliate\User' );
    $this->assert_equal( $user->get( Test::TEST_META_KEY ), Test::TEST_META_VALUE, 'User created via test should have test meta' );
    $this->assert_user_is_affiliate( $user->ID, "User should get an affid after create" );
  }


  public function test_create_empty_commission(): void {
    $commission = $this->create_test_commission();
    $this->assert_commission_exists( $commission->get_id(), 'Commission should exist after create' );
    
    // Delete commission
    $this->delete_test_commissions();
    $this->assert_commission_not_exists( $commission->get_id(), 'Commission should not exist after delete' );
  }


  public function test_create_commission(): void {
    $commission_data = [
      'date' => '2020-12-06',
      'amount' => '50',
      'description' => 'Full info',
      'transaction_id' => '1vG92ClVTga2GIROZ5fEK8JQ1GL7gkCk',
      'campaign' => 'facebook',
      'actor_id' => '64',
      'earner_id' => '144',
      'payout_date' => '2020-04-04',
      'payout_method' => 'Bitcoin',
      'payout_address' => 'sd54f5s4df6sd5f16sd54fs5d4f',
      'payout_transaction_id' => '54654-5464',
    ];
    $commission = $this->create_test_commission( $commission_data );
    $commission_data_from_db = $this->get_commission_array( $commission->get_id() );

    $this->assert_commission_exists( $commission->get_id(), 'Commission should exist after create' );
    $this->assert_equal( $commission_data['date'], $commission_data_from_db['date'] );
    $this->assert_equal( $commission_data['amount'], $commission_data_from_db['amount'] );
    $this->assert_equal( $commission_data['description'], $commission_data_from_db['description'] );
    $this->assert_equal( $commission_data['transaction_id'], $commission_data_from_db['transaction_id'] );
    $this->assert_equal( $commission_data['campaign'], $commission_data_from_db['campaign'] );
    $this->assert_equal( $commission_data['actor_id'], $commission_data_from_db['actor_id'] );
    $this->assert_equal( $commission_data['earner_id'], $commission_data_from_db['earner_id'] );
    $this->assert_equal( $commission_data['payout_date'], $commission_data_from_db['payout_date'] );
    $this->assert_equal( $commission_data['payout_method'], $commission_data_from_db['payout_method'] );
    $this->assert_equal( $commission_data['payout_address'], $commission_data_from_db['payout_address'] );
    $this->assert_equal( $commission_data['payout_transaction_id'], $commission_data_from_db['payout_transaction_id'] );

    // Delete commission
    $this->delete_test_commissions();
    $this->assert_commission_not_exists( $commission->get_id(), 'Commission should not exist after delete' );
  }


  public function test_create_empty_post(): void {
    $post = $this->create_test_post();
    $post_from_db = \get_post( $post->ID, 'OBJECT' );
    $this->assert_instanceof( $post, 'WP_Post', "Test post should be instance of WP_Post" );
    $this->assert_equal( $post, $post_from_db, "Generated post should equal post from db" );
    
    // Delete post
    $this->delete_test_posts();
    $post_from_db = \get_post( $post->ID, 'OBJECT' );
    $this->assert_equal( $post_from_db, null, "get_post() should return null after delete posts" );
  }


  public function test_create_post(): void {
    $post_data = [
      'post_title' => ranstr(),
      'post_content' => '#test ' . ranstr(),
      'post_excerpt' => ranstr(),
      'post_name' => ranstr(),
    ];
    $post = $this->create_test_post( $post_data );
    $post_from_db = \get_post( $post->ID, 'OBJECT' );
    $this->assert_instanceof( $post, 'WP_Post', "Test post should be instance of WP_Post" );
    $this->assert_equal( $post, $post_from_db, "Generated post should equal post from db" );
    
    // Delete post
    $this->delete_test_posts();
    $post_from_db = \get_post( $post->ID, 'OBJECT' );
    $this->assert_equal( $post_from_db, null, "get_post() should return null after delete posts" );
  }


  public function test_set_user(): void {
    $this->set_user();
    $this->_injected_user = new User();
    $this->_injected_user->user_login = ranstr();
    $this->_injected_user->first_name = ranstr();
    $this->_injected_user->last_name = ranstr();
    $this->_injected_user->user_email = ranstr() . '@asdqwe.orgs';
    $this->assert_equal( $this->_injected_user, User::current(), "Injected user should equal current user after set" );
    
    // Unset user
    $this->unset_user();
    $this->assert_not_equal( $this->_injected_user, User::current(), "Injected user should NOT should equal current user after unset" );
    $this->assert_false( User::current()->exists(), "Current user should be nonexistent user after unset" );
  }


  public function test_set_sponsor(): void {
    $this->set_sponsor();
    $this->_injected_sponsor = new User();
    $this->_injected_sponsor->user_login = ranstr();
    $this->_injected_sponsor->first_name = ranstr();
    $this->_injected_sponsor->last_name = ranstr();
    $this->_injected_sponsor->user_email = ranstr() . '@asdqwe.orgs';
    $this->assert_equal( $this->_injected_sponsor, User::current()->sponsor(), "Injected sponsor should equal current sponsor after set" );
    
    // Unset Sponsor
    $this->unset_sponsor();
    $this->assert_not_equal( $this->_injected_sponsor, User::current()->sponsor(), "Injected should NOT equal current sponsor after unset" );
    $this->assert_false( User::current()->sponsor()->exists(), "Current sponsor should be nonexistent user after unset" );
  }


  public function test_create_referral(): void {
    $user = $this->create_test_user();
    $downline = $this->create_test_user();
    $campaign = 'test_campaign_hello_123_0973';
    $this->create_test_referral( $downline->ID, $user->ID, $campaign );
    
    $this->assert_user_has_sponsor( $downline->ID, "Downline should have sponsor after create_test_referral()" );
    $this->assert_user_has_referral( $user->ID, $downline->ID, "User should have Downline as a referral after create_test_referral()" );
    $this->assert_user_campaign_is( $downline->ID, $campaign );

    // Delete users
    $this->delete_test_users();
    $this->assert_user_not_has_sponsor( $downline->ID, "Downline should have no sponsor after delete" );
    $this->assert_user_not_has_referrals( $user->ID, "User should have no referrals after delete" );
  }


  public function test_create_clicks(): void {
    $user = $this->create_test_user();
    $no_campaign_clicks = 2;
    $campaign_clicks = 3;
    $campaign_name = 'mos_affiliate_test_' . ranstr();
    
    for ( $i=0; $i<$no_campaign_clicks; $i++ ) {
      $this->create_test_click( $user->ID );
    }

    for ( $i=0; $i<$campaign_clicks; $i++ ) {
      $this->create_test_click( $user->ID, $campaign_name );
    }

    $this->assert_user_clicks_equal( $user->ID, $no_campaign_clicks + $campaign_clicks );
    $this->assert_user_clicks_equal( $user->ID, $campaign_clicks, $campaign_name );
    $this->assert_user_clicks_equal( $user->ID, $no_campaign_clicks, '' );
    
    // Delete users
    $this->delete_test_users();
    $this->assert_user_clicks_equal( $user->ID, 0 );
  }


  private function get_commission_array( int $id ): array {
    global $wpdb;
    $table = $wpdb->prefix . \MOS\Affiliate\Migration\CommissionsMigration::TABLE_NAME;
    $query = "SELECT * FROM $table WHERE id = $id";
    $commission_array = (array) $wpdb->get_row( $query, \ARRAY_A );
    return $commission_array;
  }


}