<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\User;
use MOS\Affiliate\Database;

use function \do_shortcode;
use function \get_user_by;
use function \add_filter;
use function \wp_insert_user;
use function \wp_delete_user;
use function \update_user_meta;

class UserShortcodeTest extends Test {

  private $user;
  private $username = 'teEGRaghlR83SBEOfMCfYjNO4NIrHZvN';
  private $email = 'teEGRaghlR83SBEOfMCfYjNO4NIrHZvN@gmail.com';
  private $first_name = 'Hayasaka';
  private $last_name = 'Ai';
  private $level_slug = 'monthly_partner';
  private $level_name = 'Monthly Partner';


  public function __construct() {
    $prev_user = get_user_by( 'login', $this->username );
    if ( $prev_user ) {
      $this->delete_user( $prev_user->ID );
    }
    
    $this->user = $this->create_user( $this->username );
  }


  public function __destruct() {
    $this->delete_user( $this->user->ID );
  }


  public function test_main(): void {
    $this->assert_true( 1==1 );
  }


  public function test_affid_shortcode(): void {
    $this->set_user( $this->user );
    $affid = $this->user->get_affid();
    $shortcode = do_shortcode( '[mos_affid]' );
    $this->assert_equal( $affid, $shortcode, [
      'affid' => $affid,
      'user' => $this->user,
      'shortcode' => $shortcode,
    ] );
  }


  public function test_email_shortcode(): void {
    $this->user->user_email = $this->email;
    $this->set_user( $this->user );
    $shortcode = do_shortcode( '[mos_email]' );
    $this->assert_equal( $shortcode, $this->email, [
      'expected' => $this->email,
      'actual' => $shortcode,
    ] );
  }


  public function test_first_name(): void {
    $this->user->first_name = $this->first_name;
    $this->set_user( $this->user );
    $shortcode = do_shortcode( '[mos_first_name]' );
    $this->assert_equal( $shortcode, $this->first_name, [
      'expected' => $this->first_name,
      'actual' => $shortcode,
    ] );
  }


  public function test_last_name(): void {
    $this->user->last_name = $this->last_name;
    $this->set_user( $this->user );
    $shortcode = do_shortcode( '[mos_last_name]' );
    $this->assert_equal( $shortcode, $this->last_name, [
      'expected' => $this->last_name,
      'actual' => $shortcode,
      ] );
    }
    

  public function test_level_shortcode(): void {
    $this->user->roles = [$this->level_slug];
    $this->set_user( $this->user );
    $shortcode = do_shortcode( '[mos_level]' );
    $this->assert_equal( $shortcode, $this->level_name, [
      'expected' => $this->level_name,
      'actual' => $shortcode,
    ] );
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

    $this->set_user( $this->user );

    $expected = $mis['gr'];
    $shortcode = '[mos_mis network=gr]';
    $shortcode_output = do_shortcode( $shortcode );
    $this->assert_equal_strict( $expected, $shortcode_output, [
      'expected' => $expected,
      'actual' => $shortcode_output,
      'shortcode' => $shortcode,
    ] );

    $expected = $mis['cm'];
    $shortcode = '[mos_mis network=non_existent]';
    $shortcode_output = do_shortcode( $shortcode );
    $this->assert_equal_strict( $expected, $shortcode_output, [
      'expected' => $expected,
      'actual' => $shortcode_output,
      'shortcode' => $shortcode,
    ] );

    $expected = '';
    $shortcode = '[mos_mis network=cm]';
    $shortcode_output = do_shortcode( $shortcode );
    $this->assert_equal_strict( $expected, $shortcode_output, [
      'expected' => $expected,
      'actual' => $shortcode_output,
      'shortcode' => $shortcode,
    ] );
  }


  public function test_name_shortcode(): void {
    $first_name = 'Hayasaka';
    $last_name = 'Ai';
    $this->user->first_name = $first_name;
    $this->user->last_name = $last_name;
    $this->set_user( $this->user );

    $expected = "$first_name $last_name";
    $shortcode = '[mos_name]';
    $output = do_shortcode( $shortcode );
    $this->assert_equal_strict( $expected, $output, [
      'expected' => $expected,
      'shortcode' => $shortcode,
      'output' => $output,
    ] );
  }


  private function set_user( User $user ): void {
    add_filter( 'mos_current_user', function() use ($user) {
      return $user;
    } );
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