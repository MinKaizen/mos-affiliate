<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\User;
use \WP_CLI;
use \WP_Post;

use function \wp_insert_user;
use function \wp_delete_user;
use function \get_user_by;
use function \remove_filter;
use function \add_filter;
use function \wp_insert_post;
use function \wp_delete_post;
use function \get_post;
use function \wp_set_post_tags;

class AccessRedirectTest extends Test {

  private $user;
  private $username = '3TQSX6qfj22oX7tgB5zIpV3RPZePfDAA';
  private $post;


  public function __construct() {
    $prev_user = get_user_by( 'login', $this->username );

    if ( $prev_user ) {
      $this->delete_user( $prev_user->ID );
    }

    $this->user = $this->create_user( $this->username );
    $this->set_user();
    $this->post = $this->create_post();
  }


  public function __destruct() {
    $this->delete_user( $this->user->ID );
    $this->unset_user();
    $this->delete_post( $this->post->ID );
  }


  public function get_user(): User {
    return $this->user;
  }


  private function set_user(): void {
    add_filter( 'mos_current_user', [$this, 'get_user'] );
    $this->db_notice( "filter added: {$this->user->ID}" );
  }


  private function unset_user(): void {
    $remove_success = remove_filter( 'mos_current_user', [$this, 'get_user'] );
    if ($remove_success) {
      $this->db_notice("filter removed: {$this->user->ID}");
    }
  }


  private function assert_redirect( string $start, string $expected_redirect, ...$data ) {
    $actual_redirect = $this->get_redirect( $start );
    $this->assert_equal( $expected_redirect, $actual_redirect, $data );
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
    $this->db_notice( "user created: $id" );
    $user = User::from_id( $id );
    return $user;
  }


  private function delete_user( int $id ): void {
    wp_delete_user( $id );
    $user_exists = (get_user_by( 'id', $id ) !== false);
    $this->assert_false_strict( $user_exists );
    $this->db_notice( "user deleted: $id" );
  }


  private function create_post(): WP_Post {
    $post_data = [
      'post_author' => 1,
      'post_title' => $this->ran_str(),
      'post_status' => 'public',
    ];
    $post_id = wp_insert_post( $post_data, false );
    $this->assert_not_equal_strict( $post_id, 0 );
    $post = get_post( $post_id, 'OBJECT' );
    $this->assert_instanceof( $post, 'WP_Post' );
    $this->db_notice( "post created: $post_id" );
    return $post;
  }


  private function delete_post( int $post_id ): void {
    wp_delete_post( $post_id, true );
    $post = get_post( $post_id );
    $this->assert_true( empty( $post ), $post );
    $this->db_notice( "post deleted: $post_id" );
  }

  private function ran_str( int $length=16): string {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
  }


}