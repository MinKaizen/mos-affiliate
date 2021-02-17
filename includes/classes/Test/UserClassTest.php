<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\User;

use function \MOS\Affiliate\ranstr;

class UserClassTest extends Test {


  public function test_construct(): void {
    $user = new User();
    $this->assert_instanceof( $user, 'MOS\Affiliate\User' );
  }


  public function test_construct_from_id(): void {
    $inserted_user = $this->create_test_user();
    $get_user = User::from_id( $inserted_user->ID );
    $this->assert_equal( $inserted_user, $get_user, [
      'User created via insert' => $inserted_user,
      'User created via User::from_id()' => $get_user,
    ] );
  }


  public function test_construct_from_affid(): void {
    $inserted_user = $this->create_test_user();
    $get_user = User::from_affid( $inserted_user->get_affid() );
    $this->assert_equal( $inserted_user, $get_user, [
      'User created via insert' => $inserted_user,
      'User created via User::from_affid()' => $get_user,
    ] );
  }


  public function test_construct_from_username(): void {
    $inserted_user = $this->create_test_user();
    $get_user = User::from_username( $inserted_user->user_login );
    $this->assert_equal( $inserted_user, $get_user, [
      'User created via insert' => $inserted_user,
      'User created via User::from_username()' => $get_user,
    ] );
  }


  public function test_existence(): void {
    $user = $this->create_test_user();
    $this->assert_true( User::id_exists( $user->ID ), "User ID should exist after create", ['user' => $user] );
    $this->assert_true( User::affid_exists( $user->get_affid() ), "Affid should exist after create", ['user' => $user] );
    $this->assert_true( User::username_exists( $user->user_login ), "Username should exist after create", ['user' => $user] );
    $this->assert_true( User::email_exists( $user->user_email ), "Email should exist after create", ['user' => $user] );
    $this->assert_true( $user->exists(), "User->exist() should be true after create", ['user' => $user] );

    $empty_user = new User();
    $this->assert_false( $empty_user->exists(), 'empty_user->exists() should be false' );

    $generated_user = new User();
    $generated_user->user_email = $user->user_email;
    $this->assert_true( $generated_user->exists(), 'User should exist if email is taken', ['generated_user' => $generated_user] );
    
    $generated_user = new User();
    $generated_user->user_login = $user->user_login;
    $this->assert_true( $generated_user->exists(), 'User should exist if username is taken', ['generated_user' => $generated_user] );
    
    $generated_user = new User();
    $generated_user->ID = $user->ID;
    $this->assert_true( $generated_user->exists(), 'User should exist if ID is taken', ['generated_user' => $generated_user] );
  }


  public function test_get_wpid(): void {
    $test_user_id = 42;
    $user = new User();
    $user->ID = $test_user_id;
    $this->assert_equal_strict( $user->get_wpid(), $test_user_id );
  }


  public function test_get_affid(): void {
    $user = $this->create_test_user();
    $this->assert_not_empty( $user->get_affid(), "UAP should automatically populate affid after insert. Check UAP settings" );
  }


  public function test_get_username(): void {
    $test_username = 'bfJd1shZEXTWpmFn2QSop6l8pGGkxfdR';
    $user = new User();
    $user->user_login = $test_username;
    $this->assert_equal_strict( $user->get_username(), $test_username );
  }


  public function test_get_name(): void {
    $first_name = 'hayasaka';
    $last_name = 'ai';
    $full_name = 'hayasaka ai';

    $user = new User();
    $user->first_name = $first_name;
    $user->last_name = $last_name;

    $this->assert_equal_strict( $user->get_first_name(), ucwords( $first_name ) );
    $this->assert_equal_strict( $user->get_last_name(), ucwords( $last_name ) );
    $this->assert_equal_strict( $user->get_name(), ucwords( $full_name ) );
  }


  public function test_get_email(): void {
    $email = 'fuu.houhou@gmail.com';
    $user = new User();
    $user->user_email = $email;
    $this->assert_equal_strict( $user->get_email(), $email );
  }


  public function test_get_mis(): void {
    $user = $this->create_test_user();
    
    $mis_slug = 'non_existent';
    $mis_meta_key = 'mos_mis_non_existent';
    $mis_value = 'some_value';
    $this->assert_equal_strict( $user->get_mis( $mis_slug ), '', "get_mis() should return empty string if mis not set" );
    \update_user_meta( $user->ID, $mis_meta_key, $mis_value );
    $this->assert_equal_strict( $user->get_mis( $mis_slug ), '', "get_mis() should return empty string if mis doesn't exist in config" );
    
    $mis_slug = 'gr';
    $mis_meta_key = 'mos_mis_gr';
    $mis_value = 'some_value';
    $this->assert_equal_strict( $user->get_mis( $mis_slug ), '', "get_mis() should return empty string if mis not set" );
    \update_user_meta( $user->ID, $mis_meta_key, $mis_value );
    $this->assert_equal_strict( $user->get_mis( $mis_slug ), $mis_value );
    
    $mis_slug = 'cb';
    $mis_meta_key = 'mos_mis_cb';
    $mis_value = 'some_value';
    $this->assert_equal_strict( $user->get_mis( $mis_slug ), '', "get_mis() should return empty string if mis not set" );
    \update_user_meta( $user->ID, $mis_meta_key, $mis_value );
    $this->assert_equal_strict( $user->get_mis( $mis_slug ), $mis_value );
    
    $mis_slug = 'cm';
    $mis_meta_key = 'mos_mis_cm';
    $mis_value = 'some_value';
    $this->assert_equal_strict( $user->get_mis( $mis_slug ), '', "get_mis() should return empty string if mis not set" );
    \update_user_meta( $user->ID, $mis_meta_key, $mis_value );
    $this->assert_equal_strict( $user->get_mis( $mis_slug ), $mis_value );
  }


  public function test_get_mis_link(): void {
    $user = $this->create_test_user();
    $slug = 'gr';
    $meta_key = 'mos_mis_gr';
    $meta_value = 'gr_affid';
    $expected_link = 'https://secure.getresponse.com/pricing/en?a=gr_affid&c=myonlinestartup';
    \update_user_meta( $user->ID, $meta_key, $meta_value );
    $this->assert_equal( $user->get_mis_link( $slug ), $expected_link );
  }


  public function test_get_level(): void {
    $non_user = new User();
    $this->assert_equal( $non_user->get_level(), 'None', 'User level should be None for a non-user' );

    $user = $this->create_test_user();

    $this->assert_equal( $user->get_level(), 'Free Member', 'User level should be Free Member by default' );
    
    $this->user_give_access( $user->ID, 'monthly_partner' );
    $this->assert_equal( $user->get_level(), 'Monthly Partner', 'User level should be Monthly Partner after getting access to monthly_partner' );
    
    $this->user_give_access( $user->ID, 'yearly_partner' );
    $this->assert_equal( $user->get_level(), 'Yearly Partner', 'User level should be Yearly Partner after getting access to yearly_partner' );
    
    $this->user_give_access( $user->ID, 'lifetime_partner' );
    $this->assert_equal( $user->get_level(), 'Lifetime Partner', 'User level should be Lifetime Partner after getting access to lifetime_partner' );
   
    $this->user_give_access( $user->ID, 'coaching' );
    $this->assert_equal( $user->get_level(), 'Coaching', 'User level should be Coaching after getting access to coaching' );
  }


  public function test_get_next_level(): void {
    $non_user = new User();
    $this->assert_equal( $non_user->get_next_level(), 'Free Member', 'User NEXT level should be Free Member for a non-user' );

    $user = $this->create_test_user();

    $this->assert_equal( $user->get_next_level(), 'Monthly Partner', 'User NEXT level should be Monthly Partner by default' );
    
    $this->user_give_access( $user->ID, 'monthly_partner' );
    $this->assert_equal( $user->get_next_level(), 'Yearly Partner', 'User NEXT level should be Yearly Partner after getting access to monthly_partner' );
    
    $this->user_give_access( $user->ID, 'yearly_partner' );
    $this->assert_equal( $user->get_next_level(), 'Lifetime Partner', 'User NEXT level should be Lifetime Partner after getting access to yearly_partner' );
    
    $this->user_give_access( $user->ID, 'lifetime_partner' );
    $this->assert_equal( $user->get_next_level(), 'Coaching', 'User NEXT level should be Coaching after getting access to lifetime_partner' );
   
    $this->user_give_access( $user->ID, 'coaching' );
    $this->assert_equal( $user->get_next_level(), '', 'User NEXT level should be [empty] after getting access to coaching' );
  }


  public function test_get_level_slug(): void {
    $non_user = new User();
    $this->assert_equal( $non_user->get_level_slug(), '', 'User level slug should be [empty] for a non-user' );

    $user = $this->create_test_user();

    $this->assert_equal( $user->get_level_slug(), 'free', 'User level slug should be free by default' );
    
    $this->user_give_access( $user->ID, 'monthly_partner' );
    $this->assert_equal( $user->get_level_slug(), 'monthly_partner', 'User level slug should be monthly_partner after getting access to monthly_partner' );
    
    $this->user_give_access( $user->ID, 'yearly_partner' );
    $this->assert_equal( $user->get_level_slug(), 'yearly_partner', 'User level slug should be yearly_partner after getting access to yearly_partner' );
    
    $this->user_give_access( $user->ID, 'lifetime_partner' );
    $this->assert_equal( $user->get_level_slug(), 'lifetime_partner', 'User level slug should be lifetime_partner after getting access to lifetime_partner' );
   
    $this->user_give_access( $user->ID, 'coaching' );
    $this->assert_equal( $user->get_level_slug(), 'coaching', 'User level slug should be coaching after getting access to coaching' );
  }


  public function test_get_next_level_slug(): void {
    $non_user = new User();
    $this->assert_equal( $non_user->get_next_level_slug(), 'free', 'User NEXT level should be free for a non-user' );

    $user = $this->create_test_user();

    $this->assert_equal( $user->get_next_level_slug(), 'monthly_partner', 'User NEXT level should be monthly_partner by default' );
    
    $this->user_give_access( $user->ID, 'monthly_partner' );
    $this->assert_equal( $user->get_next_level_slug(), 'yearly_partner', 'User NEXT level should be yearly_partner after getting access to monthly_partner' );
    
    $this->user_give_access( $user->ID, 'yearly_partner' );
    $this->assert_equal( $user->get_next_level_slug(), 'lifetime_partner', 'User NEXT level should be lifetime_partner after getting access to yearly_partner' );
    
    $this->user_give_access( $user->ID, 'lifetime_partner' );
    $this->assert_equal( $user->get_next_level_slug(), 'coaching', 'User NEXT level should be coaching after getting access to lifetime_partner' );
   
    $this->user_give_access( $user->ID, 'coaching' );
    $this->assert_equal( $user->get_next_level_slug(), '', 'User NEXT level should be [empty] after getting access to coaching' );
  }


  public function test_get_campaign(): void {
    $campaign = 'mos_affiliate_test_campaign';
    $sponsor = $this->create_test_user();
    $downline = $this->create_test_user();
    $this->create_test_referral( $downline->ID, $sponsor->ID, $campaign );
    $this->assert_equal( $downline->get_campaign(), $campaign );
  }


  public function test_qualifies_for_mis(): void {
    $user = $this->create_test_user();

    $this->assert_false( $user->qualifies_for_mis( 'gr' ), 'User should not qualifiy for mis before setting usermeta' );
    $this->assert_false( $user->qualifies_for_mis( 'cb' ), 'User should not qualifiy for mis before setting usermeta' );
    $this->assert_false( $user->qualifies_for_mis( 'cm' ), 'User should not qualifiy for mis before setting usermeta' );

    $this->user_give_access( $user->ID, 'monthly_partner' );
    $this->assert_true( $user->qualifies_for_mis( 'gr' ), 'User should qualifiy for mis after receiving mos_access_monthly_partner usermeta' );
    $this->assert_true( $user->qualifies_for_mis( 'cb' ), 'User should qualifiy for mis after receiving mos_access_monthly_partner usermeta' );
    $this->assert_true( $user->qualifies_for_mis( 'cm' ), 'User should qualifiy for mis after receiving mos_access_monthly_partner usermeta' );

    $this->assert_false( $user->qualifies_for_mis( 'non_existent' ), 'User should not qualifiy for mis if slug is non-existent, even after receiving mos_access_monthly_partner usermeta' );

  }
  
  public function test_has_access(): void {
    $user = $this->create_test_user();

    // Monthly Partner
    $this->assert_false( $user->has_access( 'monthly_partner' ) );
    $this->user_give_access( $user->ID, 'monthly_partner');
    $this->assert_true( $user->has_access( 'monthly_partner' ) );
    
    // Yearly Partner
    $this->assert_false( $user->has_access( 'yearly_partner' ) );
    $this->user_give_access( $user->ID, 'yearly_partner');
    $this->assert_true( $user->has_access( 'yearly_partner' ) );
    
    // Facebook Toolkit
    $this->assert_false( $user->has_access( 'fb_toolkit' ) );
    $this->user_give_access( $user->ID, 'fb_toolkit');
    $this->assert_true( $user->has_access( 'fb_toolkit' ) );
    
    // Lead Gen System
    $this->assert_false( $user->has_access( 'lead_system' ) );
    $this->user_give_access( $user->ID, 'lead_system');
    $this->assert_true( $user->has_access( 'lead_system' ) );
    
    // Authority Bonus Bundle
    $this->assert_false( $user->has_access( 'authority_bonuses' ) );
    $this->user_give_access( $user->ID, 'authority_bonuses');
    $this->assert_true( $user->has_access( 'authority_bonuses' ) );
    
    // Lifetime Partner
    $this->assert_false( $user->has_access( 'lifetime_partner' ) );
    $this->user_give_access( $user->ID, 'lifetime_partner');
    $this->assert_true( $user->has_access( 'lifetime_partner' ) );
    
    // Personal Coaching with Chuck
    $this->assert_false( $user->has_access( 'coaching' ) );
    $this->user_give_access( $user->ID, 'coaching');
    $this->assert_true( $user->has_access( 'coaching' ) );
  }

  public function test_has_access_levels(): void {
    $user = $this->create_test_user();

    // Personal Coaching with Chuck
    $this->assert_false( $user->has_access( 'coaching' ) );
    $this->user_give_access( $user->ID, 'coaching');
    $this->assert_true( $user->has_access( 'monthly_partner' ), new \MOS\Affiliate\Level('coaching') );
    $this->assert_true( $user->has_access( 'yearly_partner' ), new \MOS\Affiliate\Level('coaching') );
    $this->assert_true( $user->has_access( 'lifetime_partner' ), new \MOS\Affiliate\Level('coaching') );
    $this->assert_true( $user->has_access( 'coaching' ), new \MOS\Affiliate\Level('coaching') );
  }


  public function test_get_access_list(): void {
    $user = $this->create_test_user();
    $this->assert_arrays_equal( $user->get_access_list(), ['_free'], 'User access list should include _free in it by default' );
    
    $this->user_give_access( $user->ID, 'monthly_partner' );
    $this->assert_arrays_equal( $user->get_access_list(), [
      '_free',
      'monthly_partner',
    ], 'User access list should include monthly_partner after access is granted' );
    
    $this->user_give_access( $user->ID, 'yearly_partner' );
    $this->assert_arrays_equal( $user->get_access_list(), [
      '_free',
      'monthly_partner',
      'yearly_partner',
    ], 'User access list should include yearly_partner after access is granted' );
    
    $this->user_give_access( $user->ID, 'lifetime_partner' );
    $this->assert_arrays_equal( $user->get_access_list(), [
      '_free',
      'monthly_partner',
      'yearly_partner',
      'lifetime_partner',
    ], 'User access list should include lifetime_partner after access is granted' );
    
    $this->user_give_access( $user->ID, 'coaching' );
    $this->assert_arrays_equal( $user->get_access_list(), [
      '_free',
      'monthly_partner',
      'yearly_partner',
      'lifetime_partner',
      'coaching',
    ], 'User access list should include coaching after access is granted' );
    
    $this->user_give_access( $user->ID, 'fb_toolkit' );
    $this->assert_arrays_equal( $user->get_access_list(), [
      '_free',
      'monthly_partner',
      'yearly_partner',
      'lifetime_partner',
      'coaching',
      'fb_toolkit',
    ], 'User access list should include fb_toolkit after access is granted' );
    
    $this->user_give_access( $user->ID, 'lead_system' );
    $this->assert_arrays_equal( $user->get_access_list(), [
      '_free',
      'monthly_partner',
      'yearly_partner',
      'lifetime_partner',
      'coaching',
      'fb_toolkit',
      'lead_system',
    ], 'User access list should include lead_system after access is granted' );
    
    $this->user_give_access( $user->ID, 'authority_bonuses' );
    $this->assert_arrays_equal( $user->get_access_list(), [
      '_free',
      'monthly_partner',
      'yearly_partner',
      'lifetime_partner',
      'coaching',
      'fb_toolkit',
      'lead_system',
      'authority_bonuses',
    ], 'User access list should include authority_bonuses after access is granted' );
  }

  public function test_get_access_list_levels(): void {
    $user = $this->create_test_user();

    $this->user_give_access( $user->ID, 'yearly_partner' );
    $this->assert_arrays_equal( $user->get_access_list(), [
      '_free',
      'monthly_partner',
      'yearly_partner',
    ], 'User should also get access to monthly_partner when given access to yearly_partner' );

    $this->user_give_access( $user->ID, 'coaching' );
    $this->assert_arrays_equal( $user->get_access_list(), [
      '_free',
      'monthly_partner',
      'yearly_partner',
      'lifetime_partner',
      'coaching',
    ], 'User should get access to all levels when given access to coaching' );
  }
  
  
  public function test_is_partner(): void {
    $user = $this->create_test_user();

    $this->assert_false( $user->is_partner(), ['user' => $user, 'msg' => 'User should not be a partner by default'] );
    
    $this->user_give_access( $user->ID, 'monthly_partner' );
    $this->assert_true( $user->is_partner(), ['meta' => $user->get( 'mos_access_monthly_partner' ), 'msg' => 'User should be a partner if they have access to monthly_partner'] );
    $this->user_remove_access( $user->ID, 'monthly_partner' );
    $this->assert_false( $user->is_partner(), ['meta' => $user->get( 'mos_access_monthly_partner' ), 'msg' => 'User should not if monthly_partner access is removed'] );
    
    $this->user_give_access( $user->ID, 'yearly_partner' );
    $this->assert_true( $user->is_partner(), ['meta' => $user->get( 'mos_access_yearly_partner' ), 'msg' => 'User should be a partner if they have access to yearly_partner'] );
    $this->user_remove_access( $user->ID, 'yearly_partner' );
    $this->assert_false( $user->is_partner(), ['meta' => $user->get( 'mos_access_yearly_partner' ), 'msg' => 'User should not if yearly_partner access is removed'] );
    
    $this->user_give_access( $user->ID, 'lifetime_partner' );
    $this->assert_true( $user->is_partner(), ['meta' => $user->get( 'mos_access_lifetime_partner' ), 'msg' => 'User should be a partner if they have access to lifetime_partner'] );
    $this->user_remove_access( $user->ID, 'lifetime_partner' );
    $this->assert_false( $user->is_partner(), ['meta' => $user->get( 'mos_access_lifetime_partner' ), 'msg' => 'User should not if lifetime_partner access is removed'] );
  }


  public function test_get_referral_ids(): void {
    $this->_injected_user = $this->create_test_user();
    $referral1 = $this->create_test_user();
    $referral1->db_add_sponsor( $this->_injected_user );
    $this->assert_equal( $referral1->sponsor(), $this->_injected_user );
    $referral2 = $this->create_test_user();
    $referral2->db_add_sponsor( $this->_injected_user );
    $this->assert_equal( $referral2->sponsor(), $this->_injected_user );
    $referral3 = $this->create_test_user();
    $referral3->db_add_sponsor( $this->_injected_user );
    $this->assert_equal( $referral3->sponsor(), $this->_injected_user );

    $referral_ids = $this->_injected_user->get_referral_ids();
    $this->assert_equal( count( $referral_ids ), 3, ['$referral_ids' => $referral_ids] );
    $this->assert_contains( $referral_ids, $referral1->get_wpid() );
    $this->assert_contains( $referral_ids, $referral2->get_wpid() );
    $this->assert_contains( $referral_ids, $referral3->get_wpid() );
  }


  public function test_get_referrals(): void {
    $this->_injected_user = $this->create_test_user();
    $referral1 = $this->create_test_user();
    $referral1->db_add_sponsor( $this->_injected_user );
    $this->assert_equal( $referral1->sponsor(), $this->_injected_user );
    $referral2 = $this->create_test_user();
    $referral2->db_add_sponsor( $this->_injected_user );
    $this->assert_equal( $referral2->sponsor(), $this->_injected_user );
    $referral3 = $this->create_test_user();
    $referral3->db_add_sponsor( $this->_injected_user );
    $this->assert_equal( $referral3->sponsor(), $this->_injected_user );

    $referrals = $this->_injected_user->get_referrals();
    $this->assert_equal( count( $referrals ), 3, ['$referrals' => $referrals] );
    $this->assert_contains( $referrals, $referral1 );
    $this->assert_contains( $referrals, $referral2 );
    $this->assert_contains( $referrals, $referral3 );
  }


  public function test_get_commissions(): void {
    #todo
  }


  public function test_db(): void {
    global $wpdb;
    
    $username = ranstr();
    $password = ranstr();
    $user = new User();
    $user->user_login = $username;
    $user->user_pass = $password;
    
    $sponsor_username = ranstr();
    $sponsor_password = ranstr();
    $sponsor = new User();
    $sponsor->user_login = $sponsor_username;
    $sponsor->user_pass = $sponsor_password;

    // insert user()
    $user->db_insert();
    $this->assert_not_empty( $wpdb->insert_id, 'wpdb->insert_id should not be empty after user->db_insert()' );
    $db_user = \get_user_by( 'login', $username );
    $this->assert_not_empty( $db_user, "Username $username should exist after db_insert()" );
    $this->assert_equal( $user->ID, $db_user->ID, "user->ID should equal db_user->ID after insert" );
    $this->assert_not_empty( $user->get_affid(), "UAP should auto register affid after db_insert(). Check UAP settings" );

    // add sponsor
    $sponsor->db_insert();
    $user->db_add_sponsor( $sponsor);
    $db_sponsor = $user->sponsor();
    $this->assert_equal( $sponsor->ID, $db_sponsor->ID, "Sponsor in DB should be same as sponsor in variable", $sponsor, $db_sponsor );

    // delete user
    $affid = $user->get_affid();
    $id = $user->ID;
    $user->db_delete();
    $this->assert_false( User::affid_exists( $affid ), "Affid $affid should be deleted after user->db_delete()" );
    $this->assert_false( User::id_exists( $id ), "User ID $id should not exist after user->db_delete" );
    $this->assert_false( $user->sponsor()->exists(), "Sponsor relationship should be deleted after user->db_delete()" );
    
    // delete sponsor
    $sponsor_affid = $sponsor->get_affid();
    $sponsor_id = $sponsor->ID;
    $sponsor->db_delete();
    $this->assert_false( User::affid_exists( $sponsor_affid ), "Affid $sponsor_affid should be deleted after sponsor->db_delete()" );
    $this->assert_false( User::id_exists( $sponsor_id ), "User ID $sponsor_id should not exist after sponsor->db_delete" );
  }


}