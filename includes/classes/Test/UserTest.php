<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\User;
use MOS\Affiliate\Database;
use \WP_CLI;
use MOS\Affiliate\Mis;

class UserTest extends Test {

  private $user_ids_to_delete = [];
  private $user_username = 'JGvDwdQPVp0DHDzeUog9HftVeajzpqCv';
  private $sponsor_username = 'rEW2i41jztjYawCHbz8ImrcVSrkM95kr';


  public function __construct() {
    $prev_user = \get_user_by( 'login', $this->user_username );
    if ( $prev_user ) {
      $this->delete_user( $prev_user->ID );
    }
    
    $prev_sponsor = \get_user_by( 'login', $this->sponsor_username );
    if ( $prev_sponsor ) {
      $this->delete_user( $prev_sponsor->ID );
    }
    
    $user_id = $this->create_user( $this->user_username );
    $sponsor_id = $this->create_user( $this->sponsor_username );

    $this->user_ids_to_delete[] = $user_id;
    $this->user_ids_to_delete[] = $sponsor_id;

    $db = new Database();
    $add_sponsor_success = $db->add_sponsor( $user_id, $sponsor_id);
    $this->assert_true_strict( $add_sponsor_success );
    $this->assert_true_strict( $db->user_has_sponsor( $user_id ) );
    $this->db_notice( "$user_id - added sponsor ($sponsor_id)" );
  }


  public function __destruct() {
    foreach ( $this->user_ids_to_delete as $id ) {
      $this->delete_user( $id );
    }
  }


  public function test_construct(): void {
    $user = new User();
    $this->assert_instanceof( $user, 'MOS\Affiliate\User' );
  }


  public function test_is_empty(): void {
    $user = new User();
    $this->assert_true( $user->is_empty() );
  }


  public function test_get_wpid(): void {
    $test_user_id = 42;
    $user = new User();
    $user->id = $test_user_id;
    $this->assert_equal_strict( $user->get_wpid(), $test_user_id );
  }


  public function test_get_affid(): void {
    $user = new User();
    $this->assert_equal( $user->get_affid(), 0 );
  }


  public function test_get_username(): void {
    $test_username = 'bfJd1shZEXTWpmFn2QSop6l8pGGkxfdR';
    $user = new User();
    $user->user_login = $test_username;
    $this->assert_equal_strict( $user->get_username(), $test_username );
  }


  public function test_get_name(): void {
    $first_name = 'Hayasaka';
    $last_name = 'Ai';
    $full_name = 'Hayasaka Ai';

    $user = new User();
    $user->first_name = $first_name;
    $user->last_name = $last_name;

    $this->assert_equal_strict( $user->get_first_name(), $first_name );
    $this->assert_equal_strict( $user->get_last_name(), $last_name );
    $this->assert_equal_strict( $user->get_name(), $full_name );
  }


  public function test_get_email(): void {
    $email = 'fuu.houhou@gmail.com';
    $user = new User();
    $user->user_email = $email;
    $this->assert_equal_strict( $user->get_email(), $email );
  }


  public function test_get_mis(): void {
    $user = User::from_username( $this->user_username );

    $id = $user->get_wpid();

    $mis_slug = 'key_not_in_config';
    $mis_meta_key = \MOS\Affiliate\MIS_META_KEY_PREFIX . $mis_slug;
    $mis_value = 'some_value';
    $success = \update_user_meta( $id, $mis_meta_key , $mis_value );
    $this->assert_true( $success );
    $this->assert_equal_strict( $user->get_mis( $mis_slug ), '' );

    $mis_slug = 'gr';
    $mis_meta_key = \MOS\Affiliate\MIS_META_KEY_PREFIX . $mis_slug;
    $mis_value = 'some_value';
    $success = \update_user_meta( $id, $mis_meta_key, $mis_value );
    $this->assert_true( $success );
    $this->assert_equal_strict( $user->get_mis( $mis_slug ), $mis_value );
  }


  public function test_get_level(): void {
    $user = new User();
    $user->roles = ['hello_world'];
    $this->assert_equal_strict( $user->get_level(), 'Hello World' );
    $user->roles = ['free'];
    $this->assert_equal_strict( $user->get_level(), 'Free Member' );
    $user->roles = ['monthly_partner'];
    $this->assert_equal_strict( $user->get_level(), 'Monthly Partner' );
    $user->roles = ['yearly_partner'];
    $this->assert_equal_strict( $user->get_level(), 'Yearly Partner' );
  }


  public function test_qualifies_for_mis(): void {
    $user = new User();
    
    $slug = 'gr';
    $mis = \MOS\Affiliate\MIS_NETWORKS['gr'];
    $this->assert_false_strict( empty( $mis ) );
    $this->assert_has_key( $mis, 'cap' );
    $cap = $mis['cap'];

    $this->assert_false_strict( $user->qualifies_for_mis( $slug ) );
    $user->add_cap( $cap );
    $this->assert_true_strict( $user->qualifies_for_mis( $slug ) );
  }


  public function test_sponsor(): void {
    $user = User::from_username( $this->user_username );
    $sponsor = $user->sponsor();
    $this->assert_equal_strict( $sponsor->user_login, $this->sponsor_username );
  }


  private function set_user( User $user ): void {
    \add_filter( 'mos_current_user', function() use ($user) {
      return $user;
    } );
  }


  private function create_user( string $username ): int {
    // Create User
    $id = \wp_insert_user([
      'user_login' => $username,
    ]);
    $this->assert_is_int( $id, $id );
    $this->db_notice( "$id - user created" );

    // Register user as affiliate
    $db = new Database();
    $success = $db->register_affiliate( $id );
    $this->assert_true_strict( $success );
    $this->db_notice( "$id - registered as affiliate" );

    return $id;
  }


  private function delete_user( int $id ): void {
    global $wpdb;

    // Delete User
    \wp_delete_user( $id );
    $this->db_notice( "$id - user deleted" );
    $this->assert_false_strict( $this->wpid_exists( $id ) );

    // Remove affiliate ID
    $table = $wpdb->prefix . 'uap_affiliates';
    $columns = ['uid' => $id];
    $formats = ['uid' => '%d'];
    $rows_deleted = $wpdb->delete( $table, $columns, $formats );
    $this->db_notice( "$id - removed affiliate" );
    $this->assert_not_equal_strict( $rows_deleted, false );
    
    // Remove sponsor
    $table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
    $columns = ['referral_wp_uid' => $id];
    $formats = ['referral_wp_uid' => '%d'];
    $rows_deleted = $wpdb->delete( $table, $columns, $formats );
    $this->db_notice( "$id - removed sponsor" );
    $this->assert_not_equal_strict( $rows_deleted, false );
  }


  private function wpid_exists( int $id ): bool {
    $user = \get_user_by( 'id', $id );
    return !empty( $user );
  }


}