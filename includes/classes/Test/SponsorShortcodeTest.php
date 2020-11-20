<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\User;
use MOS\Affiliate\Database;
use \WP_CLI;
use MOS\Affiliate\Mis;

use function \do_shortcode;
use function \get_user_by;
use function \add_filter;
use function \remove_filter;
use function \wp_insert_user;
use function \wp_delete_user;
use function \update_user_meta;

class SponsorShortcodeTest extends Test {

  const INJECTION_PRIORITY = 39;

  private $user;
  private $sponsor;
  private $user_username = 'U4xJsCmznHzKDAPp03540Nc12ZGthVA8';
  private $sponsor_username = 'qO5q0I73ifiSpc7VQktkL0SDJeJLGde7';
  private $mis = [
    'gr' => 'my_gr_id',
    'cm' => '',
    'non_existent' => 'my_nonexistent_id',
  ];


  public function __construct() {
    $prev_user = get_user_by( 'login', $this->user_username );
    if ( $prev_user ) {
      $this->delete_user( $prev_user->ID );
    }
    
    $prev_sponsor = get_user_by( 'login', $this->sponsor_username );
    if ( $prev_sponsor ) {
      $this->delete_user( $prev_sponsor->ID );
    }
    
    $this->user = $this->create_user( $this->user_username );
    $this->sponsor = $this->create_user( $this->sponsor_username );

    // Give MIS to Sponsor
    foreach( $this->mis as $slug => $value ) {
      $meta_key = \MOS\Affiliate\MIS_META_KEY_PREFIX . $slug;
      update_user_meta( $this->sponsor->ID, $meta_key, $value );
    }

    $this->set_user();
    $this->set_sponsor();
  }


  public function __destruct() {
    $this->delete_user( $this->user->ID );
    $this->delete_user( $this->sponsor->ID );
    $this->unset_user();
    $this->unset_sponsor();
  }


  public function test_sponsor_affid_shortcode(): void {
    $expected = $this->sponsor->get_affid();
    $shortcode = '[mos_sponsor_affid]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  public function test_sponsor_email_shortcode(): void {
    $email = 'teEGRaghlR83SBEOfMCfYjNO4NIrHZvN@gmail.com';
    $this->sponsor->user_email = $email;
    $shortcode = '[mos_sponsor_email]';
    $this->assert_shortcode_equal( $shortcode, $email );
  }


  public function test_sponsor_first_name(): void {
    $first_name = 'Hayasaka';
    $this->sponsor->first_name = $first_name;
    $shortcode = '[mos_sponsor_first_name]';
    $this->assert_shortcode_equal( $shortcode, $first_name );
  }


  public function test_sponsor_last_name(): void {
    $last_name = 'Ai';
    $this->sponsor->last_name = $last_name;
    $shortcode = '[mos_sponsor_last_name]';
    $this->assert_shortcode_equal( $shortcode, $last_name );
  }
    

  public function test_sponsor_level_shortcode(): void {
    $level_slug = 'monthly_partner';
    $level_name = 'Monthly Partner';
    $this->sponsor->roles = [$level_slug];
    $shortcode = '[mos_sponsor_level]';
    $this->assert_shortcode_equal( $shortcode, $level_name );
  }
    

  public function test_sponsor_mis_shortcode(): void {
    // User not logged in --> show default
    $this->unset_user();
    $this->assert_mis( 'gr', Mis::get_default( 'gr' ) );
    $this->assert_mis( 'cm', Mis::get_default( 'cm' ) );
    $this->assert_mis( 'cb', Mis::get_default( 'cb' ) );
    $this->set_user();
    
    // User has no caps --> show default
    $mis_slug = 'gr';
    $this->assert_mis( 'gr', Mis::get_default( 'gr' ) );
    $this->assert_mis( 'cm', Mis::get_default( 'cm' ) );
    $this->assert_mis( 'cb', Mis::get_default( 'cb' ) );

    // Add caps
    foreach ( $this->mis as $slug => $value ) {
      $mis = Mis::get( $slug );
      if ( $mis->exists() ) {
        $cap = $mis->cap;
        $this->sponsor->add_cap( $cap );
      }
    }

    // Has cap --> show value
    $this->assert_mis( 'gr', $this->mis['gr'] );

    // Has cap but value is empty --> show default value
    $this->assert_mis( 'cm', Mis::get_default( 'cm' ) );

    // mis slug not in config --> show nothing
    $this->assert_mis( 'non_existent', '' );

    // did not fill in mis --> show default
    $this->assert_mis( 'cb', Mis::get_default( 'cb' ) );
  }


  public function test_sponsor_name_shortcode(): void {
    $first_name = 'Hayasaka';
    $last_name = 'Ai';
    $this->sponsor->first_name = $first_name;
    $this->sponsor->last_name = $last_name;

    $expected = "$first_name $last_name";
    $shortcode = '[mos_sponsor_name]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  public function test_sponsor_username_shortcode(): void {
    $expected = $this->sponsor_username;
    $shortcode = '[mos_sponsor_username]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  public function test_sponsor_wpid_shortcode(): void {
    $expected = $this->sponsor->ID;
    $shortcode = '[mos_sponsor_wpid]';
    $this->assert_shortcode_equal( $shortcode, $expected );
  }


  private function assert_shortcode_equal( string $shortcode, $expected, ...$data ): void {
    $output = do_shortcode( $shortcode );
    $data[] = [
      'expected' => $expected,
      'shortcode' => $shortcode,
      'output' => $output,
    ];
    $this->assert_equal( $expected, $output, $data );
  }


  public function get_user(): User {
    return $this->user;
  }


  public function get_sponsor( $original_sponsor, $user_id ): User {
    if ( $user_id == $this->user->ID ) {
      return $this->sponsor;
    } else {
      return $original_sponsor;
    }
  }


  private function set_user(): void {
    add_filter( 'mos_current_user', [$this, 'get_user'], self::INJECTION_PRIORITY );
    WP_CLI::line('Filter added: set_user');
  }


  private function unset_user(): void {
    $remove_success = remove_filter( 'mos_current_user', [$this, 'get_user'], self::INJECTION_PRIORITY );
    if ($remove_success) {
      WP_CLI::line('Filter removed: set_user');
    }
  }


  private function set_sponsor(): void {
    add_filter( 'mos_sponsor', [$this, 'get_sponsor'], self::INJECTION_PRIORITY, 2 );
    WP_CLI::line('Filter added: set_sponsor');
  }


  private function unset_sponsor(): void {
    $remove_success = remove_filter( 'mos_sponsor', [$this, 'get_sponsor'], self::INJECTION_PRIORITY, 2 );
    if ($remove_success) {
      WP_CLI::line('Filter removed: set_sponsor');
    }
  }


  private function assert_mis( string $mis_slug, $expected, ...$data ): void {
    $shortcode = "[mos_sponsor_mis network=$mis_slug]";
    $this->assert_shortcode_equal( $shortcode, $expected );
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