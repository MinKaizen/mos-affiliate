<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;

class AccessRedirectTest extends Test {

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


  public function test_main(): void {
    foreach ( $this->urls as $pair ) {
      $this->check_redirect( $pair['start'], $pair['end'] );
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