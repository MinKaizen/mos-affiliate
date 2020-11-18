<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\User;
use MOS\Affiliate\Database;
use \WP_CLI;
use MOS\Affiliate\Mis;

class UserTest extends Test {

  private $user_ids_to_delete = [];

  private $user = [
    'username' => 'JGvDwdQPVp0DHDzeUog9HftVeajzpqCv',
    'email' => 'JGvDwdQPVp0DHDzeUog9HftVeajzpqCv@gmail.com',
  ];

  private $sponsor = [
    'username' => 'rEW2i41jztjYawCHbz8ImrcVSrkM95kr',
    'email' => 'rEW2i41jztjYawCHbz8ImrcVSrkM95kr@gmail.com',
    'mis' => [
      'gr' => 'gr42',
      'cm' => 'cm42',
    ],
  ];


  public function __construct() {
    $user = User::from_username( $this->user['username'] );
    $sponsor = User::from_username( $this->sponsor['username'] );
    
    // Create user if it doesn't exist
    if ( $user->is_empty() ) {
      $this->create_user( $this->user );
      $user = User::from_username( $this->user['username'] );
      $this->assert_false( $user->is_empty() );
    }
    
    // Create sponsor if it doesn't exist
    if ( $sponsor->is_empty() ) {
      $this->create_user( $this->sponsor );
      $sponsor = User::from_username( $this->sponsor['username'] );
      $this->assert_false( $sponsor->is_empty() );
    }

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
    // #TODO
  }


  public function test_get_level(): void {
    // #TODO
  }


  public function test_qualifies_for_mis(): void {
    // #TODO
  }


  private function set_user( User $user ): void {
    \add_filter( 'mos_current_user', function() use ($user) {
      return $user;
    } );
  }


  private function create_user( array $user_array ): void {
    // Create User
    $id = \wp_insert_user([
      'user_login' => $user_array['username'],
      'user_email' => $user_array['email'],
    ]);
    WP_CLI::warning( "#database created user: $id" );
    $this->assert_not_equal_strict( \get_user_by( 'id', $id ), false );

    // Make sure to this user once you're done testing
    $this->user_ids_to_delete[] = $id;

    // Register user as affiliate
    $db = new Database();
    $success = $db->register_affiliate( $id );
    $this->assert_true_strict( $success );
  }


  private function delete_user( int $id ): void {
    global $wpdb;

    // Delete User
    \wp_delete_user( $id );
    WP_CLI::warning( "#database deleted user: $id" );
    $this->assert_false_strict( \get_user_by( 'id', $id ) );

    // Remove affiliate ID
    $table = $wpdb->prefix . 'uap_affiliates';
    $columns = ['uid' => $id];
    $formats = ['uid' => '%d'];
    $rows_deleted = $wpdb->delete( $table, $columns, $formats );
    WP_CLI::warning( "#database removed affiliate" );
    $this->assert_not_equal_strict( $rows_deleted, false );
    
    // Remove affiliate relationships
    $table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
    $columns = ['referral_wp_uid' => $id];
    $formats = ['referral_wp_uid' => '%d'];
    $rows_deleted = $wpdb->delete( $table, $columns, $formats );
    WP_CLI::warning( "#database severed aff relationships: $rows_deleted" );
    $this->assert_not_equal_strict( $rows_deleted, false );
  }


}