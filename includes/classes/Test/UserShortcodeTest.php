<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\User;
use MOS\Affiliate\Database;

use function \do_shortcode;
use function \get_user_by;
use function \add_filter;
use function \remove_filter;
use function \wp_insert_user;
use function \wp_delete_user;
use function \update_user_meta;

class UserShortcodeTest extends Test {

  private $user;
  private $username = 'teEGRaghlR83SBEOfMCfYjNO4NIrHZvN';


  public function __construct() {
    $prev_user = get_user_by( 'login', $this->username );
    if ( $prev_user ) {
      $this->delete_user( $prev_user->ID );
    }
    
    $this->user = $this->create_user( $this->username );
    $this->set_user();
  }


  public function __destruct() {
    $this->delete_user( $this->user->ID );
    $this->unset_user();
  }


  public function test_affid_shortcode(): void {
    $expected = $this->user->get_affid();
    $shortcode = '[mos_affid]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  public function test_email_shortcode(): void {
    $email = 'teEGRaghlR83SBEOfMCfYjNO4NIrHZvN@gmail.com';
    $this->user->user_email = $email;
    $shortcode = '[mos_email]';
    $this->assert_shortcode_equal( $shortcode, $email );
  }


  public function test_first_name(): void {
    $first_name = 'Hayasaka';
    $this->user->first_name = $first_name;
    $shortcode = '[mos_first_name]';
    $this->assert_shortcode_equal( $shortcode, $first_name );
  }


  public function test_last_name(): void {
    $last_name = 'Ai';
    $this->user->last_name = $last_name;
    $shortcode = '[mos_last_name]';
    $this->assert_shortcode_equal( $shortcode, $last_name );
  }
    

  public function test_level_shortcode(): void {
    $level_slug = 'monthly_partner';
    $level_name = 'Monthly Partner';
    $this->user->roles = [$level_slug];
    $shortcode = '[mos_level]';
    $this->assert_shortcode_equal( $shortcode, $level_name );
  }
    

  public function test_mis_shortcode(): void {
    $mis = [
      'gr' => 'my_gr_id',
      'cm' => '',
      'non_existent' => 'my_nonexistent_id',
    ];

    foreach( $mis as $slug => $value ) {
      $meta_key = \MOS\Affiliate\MIS_META_KEY_PREFIX . $slug;
      update_user_meta( $this->user->ID, $meta_key, $value );
    }


    $expected = $mis['gr'];
    $shortcode = '[mos_mis network=gr]';
    $this->assert_shortcode_equal( $shortcode, $expected );

    $expected = $mis['cm'];
    $shortcode = '[mos_mis network=cm]';
    $this->assert_shortcode_equal( $shortcode, $expected );
    
    $expected = '';
    $shortcode = '[mos_mis network=non_existent]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  public function test_name_shortcode(): void {
    $first_name = 'Hayasaka';
    $last_name = 'Ai';
    $this->user->first_name = $first_name;
    $this->user->last_name = $last_name;

    $expected = "$first_name $last_name";
    $shortcode = '[mos_name]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  public function test_username_shortcode(): void {
    $expected = $this->username;
    $shortcode = '[mos_username]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  public function test_wpid_shortcode(): void {
    $expected = $this->user->ID;
    $shortcode = '[mos_wpid]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  private function assert_shortcode_equal( string $shortcode, $expected ): void {
    $output = do_shortcode( $shortcode );
    $this->assert_equal( $expected, $output, [
      'expected' => $expected,
      'shortcode' => $shortcode,
      'output' => $output,
    ] );
  }


  public function get_user(): User {
    return $this->user;
  }


  private function set_user(): void {
    add_filter( 'mos_current_user', [$this, 'get_user'] );
  }


  private function unset_user(): void {
    remove_filter( 'mos_current_user', [$this, 'get_user'] );
  }


  private function create_user( string $username ): User {
    // Create User
    $id = wp_insert_user([
      'user_login' => $username,
    ]);
    
    // Register user as affiliate
    $db = new Database();
    $success = $db->register_affiliate( $id );

    $this->assert_is_int( $id, $id );
    $this->assert_true_strict( $success );
    $this->db_notice( "$id - user created" );

    $user = User::from_id( $id );
    return $user;
  }


  private function delete_user( int $id ): void {
    // Delete User
    wp_delete_user( $id );
    $user_exists = (get_user_by( 'id', $id ) !== false);
    
    // Remove affiliate ID
    global $wpdb;
    $table = $wpdb->prefix . 'uap_affiliates';
    $columns = ['uid' => $id];
    $formats = ['uid' => '%d'];
    $rows_deleted = $wpdb->delete( $table, $columns, $formats );
    
    $this->assert_false_strict( $user_exists );
    $this->assert_not_equal_strict( $rows_deleted, false );
    $this->db_notice( "$id - user deleted" );
  }


}