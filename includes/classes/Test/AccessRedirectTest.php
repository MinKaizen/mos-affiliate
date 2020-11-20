<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\User;
use \WP_CLI;

use function \wp_insert_user;
use function \wp_delete_user;
use function \get_user_by;
use function \remove_filter;
use function \add_filter;

class AccessRedirectTest extends Test {

  private $user;
  private $username = '3TQSX6qfj22oX7tgB5zIpV3RPZePfDAA';
  private $urls = [
    [
      'start' => '/monthly-partners-only',
      'end' => '/no-access-monthly-partner',
    ],
    [
      'start' => '/yearly-partners-only',
      'end' => '/no-access-yearly-partner',
    ],
    [
      'start' => '/free-members-only',
      'end' => '/',
    ],
  ];


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


  public function test_main(): void {
    foreach ( $this->urls as $pair ) {
      $this->check_redirect( $pair['start'], $pair['end'] );
    }
  }


  public function get_user(): User {
    return $this->user;
  }


  private function set_user(): void {
    add_filter( 'mos_current_user', [$this, 'get_user'] );
    WP_CLI::line('Filter added: set_user');
  }


  private function unset_user(): void {
    $remove_success = remove_filter( 'mos_current_user', [$this, 'get_user'] );
    if ($remove_success) {
      WP_CLI::line('Filter removed: set_user');
    }
  }


  private function check_redirect( string $start, string $end ) {
    $url = \home_url( $start );
    $redirected = $this->get_redirect( $url );
    $this->assert_equal( \home_url( $end ), $redirected );
  }


  private function get_redirect( string $url ): string {
    // Initialize a CURL session. 
    $ch = curl_init(); 
      
    // Grab URL and pass it to the variable. 
    curl_setopt($ch, CURLOPT_URL, $url); 
      
    // Catch output (do NOT print!) 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
      
    // Return follow location true 
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE); 
    $html = curl_exec($ch); 
      
    // Getinfo or redirected URL from effective URL 
    $redirectedUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); 
      
    // Close handle 
    curl_close($ch); 
    // echo "Original URL:   " . $url . "<br/>"; 
    // echo "Redirected URL: " . $redirectedUrl . "<br/>"; 
    return $redirectedUrl;
  }


  private function create_user( string $username ): User {
    $id = wp_insert_user(['user_login' => $username]);
    $this->assert_is_int( $id, $id );
    $this->db_notice( "$id - user created" );
    $user = User::from_id( $id );
    return $user;
  }


  private function delete_user( int $id ): void {
    wp_delete_user( $id );
    $user_exists = (get_user_by( 'id', $id ) !== false);
    $this->assert_false_strict( $user_exists );
    $this->db_notice( "$id - user deleted" );
  }

}