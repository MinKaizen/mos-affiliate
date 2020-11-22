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
use function \home_url;
use function \wp_get_upload_dir;
use function \wp_login_url;

class AccessRedirectTest extends Test {

  private $user;
  private $username = '3TQSX6qfj22oX7tgB5zIpV3RPZePfDAA';
  private $user_pass = '5FwZsUZ8IFJ60ofVz2rgftHxDcvcrQXb';
  private $post;
  private $post_name = 'VTFJxWlLLouadrgNUZ9rdaBKdifhRdm5';
  private $permalink;
  private $cookie_file;
  private $http_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6';


  public function __construct() {
    // Check for curl
    if ( !function_exists( 'curl_init' ) || ! function_exists( 'curl_exec' ) ) {
      WP_CLI::error( __CLASS__ . 'requires curl' );
    }

    // Set cookie file for curl
    $upload_dir = (array) wp_get_upload_dir();
    $upload_basedir = $upload_dir['basedir'];
    $this->cookie_file = $upload_basedir . '/mos-affiliate/tests/access_redirect/cookies.txt';

    $prev_user = get_user_by( 'login', $this->username );

    if ( $prev_user ) {
      $this->delete_user( $prev_user->ID );
    }

    $this->user = $this->create_user( $this->username, $this->user_pass );
    $this->set_user();
    $this->post = $this->create_post();
    $this->permalink = get_permalink( $this->post->ID );
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


  private function assert_login_and_redirect( string $start, string $expected_redirect, ...$data ) {
    $actual_redirect = $this->login_and_get_redirect( $start );
    $data[] = [
      'user' => new User( $this->user->ID ),
      'expected' => $expected_redirect,
      'actual' => $actual_redirect,
    ];

    $actual_redirect = trim( $actual_redirect, '/' );
    $expected_redirect = trim( $expected_redirect, '/' );

    $this->assert_equal( $expected_redirect, $actual_redirect, $data );
  }


  private function assert_redirect( string $start, string $expected_redirect, ...$data ) {
    $actual_redirect = $this->get_redirect( $start );
    $data[] = [
      'expected' => $expected_redirect,
      'actual' => $actual_redirect,
    ];

    $actual_redirect = trim( $actual_redirect, '/' );
    $expected_redirect = trim( $expected_redirect, '/' );

    $this->assert_equal( $expected_redirect, $actual_redirect, $data );
  }


  private function login_and_get_redirect( string $url ): string {
    // Preparing postdata for wordpress login
    $data = "log=". $this->username ."&pwd=" . $this->user_pass . "&wp-submit=Log%20In&redirect_to=" . $url;
    $login_url = wp_login_url();

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $login_url );
  
    // Set the cookies for the login in a cookie file.
    curl_setopt( $ch, CURLOPT_COOKIEJAR, $this->cookie_file );
    
    // Set SSL to false
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    
    // User agent
    curl_setopt( $ch, CURLOPT_USERAGENT, $this->http_agent );
    
    // Maximum time cURL will wait for get response. in seconds
    curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
    
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1 );
    
    // Return or echo the execution
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
  
    // Set Http referer.
    curl_setopt( $ch, CURLOPT_REFERER, $login_url );
  
    // Post fields to the login url
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
    curl_setopt( $ch, CURLOPT_POST, 1);
    
    $content = curl_exec ($ch);
    $redirected_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    
    curl_close( $ch );
  
    return $redirected_url;
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


  private function create_user( string $username, string $password ): User {
    $id = wp_insert_user([
      'user_login' => $username,
      'user_pass' => $password,
    ]);
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
      'post_title' => $this->post_name,
      'post_name' => $this->post_name,
      'post_status' => 'publish',
      'post_content' => '#content: ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++',
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


}