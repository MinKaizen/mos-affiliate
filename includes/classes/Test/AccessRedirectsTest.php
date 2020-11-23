<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\AccessRedirect;
use \MOS\Affiliate\Test;
use \MOS\Affiliate\User;
use \WP_CLI;
use \WP_Post;
use \MOS\Affiliate\AccessRedirect\FreeAccessRedirect;
use \MOS\Affiliate\AccessRedirect\MonthlyPartnerAccessRedirect;
use \MOS\Affiliate\AccessRedirect\YearlyPartnerAccessRedirect;

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

class AccessRedirectsTest extends Test {

  private $user;
  private $username = '3TQSX6qfj22oX7tgB5zIpV3RPZePfDAA';
  private $user_pass = '5FwZsUZ8IFJ60ofVz2rgftHxDcvcrQXb';
  private $post;
  private $post_name = 'VTFJxWlLLouadrgNUZ9rdaBKdifhRdm5';
  private $permalink;
  private $cookie_file;
  private $accesses= [];
  private $http_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6';
  private $curl;


  public function __construct() {
    // Check for curl
    if ( !function_exists( 'curl_init' ) || ! function_exists( 'curl_exec' ) ) {
      WP_CLI::error( __CLASS__ . 'requires curl' );
    }

    // Set cookie file for curl
    $upload_dir = (array) wp_get_upload_dir();
    $upload_basedir = $upload_dir['basedir'];
    $this->cookie_file = $upload_basedir . '/mos-affiliate/tests/access_redirect/cookies.txt';

    // Set access redirects
    $this->accesses = [
      'free' => new FreeAccessRedirect(),
      'monthly_partner' => new MonthlyPartnerAccessRedirect(),
      'yearly_partner' => new YearlyPartnerAccessRedirect(),
    ];

    $prev_user = get_user_by( 'login', $this->username );

    if ( $prev_user ) {
      $this->delete_user( $prev_user->ID );
    }

    $this->user = $this->create_user( $this->username, $this->user_pass );
    $this->set_user();
    $this->post = $this->create_post();
    $this->permalink = get_permalink( $this->post->ID );
    $this->curl_init();
  }


  public function __destruct() {
    $this->delete_user( $this->user->ID );
    $this->unset_user();
    $this->delete_post( $this->post->ID );
    $this->curl_close();
  }


  public function test_free_member(): void {
    $this->user->set_role('free');
    
    $this->assert_can_access( $this->accesses['free'] );
    $this->assert_cannot_access( $this->accesses['monthly_partner'] );
    $this->assert_cannot_access( $this->accesses['yearly_partner'] );
  }


  public function test_monthly_partner(): void {
    $this->user->set_role('monthly_partner');
    
    $this->assert_can_access( $this->accesses['free'] );
    $this->assert_can_access( $this->accesses['monthly_partner'] );
    $this->assert_cannot_access( $this->accesses['yearly_partner'] );
  }


  public function test_yearly_partner(): void {
    $this->user->set_role('yearly_partner');
    
    $this->assert_can_access( $this->accesses['free'] );
    $this->assert_can_access( $this->accesses['monthly_partner'] );
    $this->assert_can_access( $this->accesses['yearly_partner'] );
  }


  public function get_user(): User {
    return $this->user;
  }


  private function assert_can_access( AccessRedirect $access, ...$data ) {
    wp_set_post_tags( $this->post->ID, $access->get_tag() );
    $this->assert_login_and_redirect( $this->permalink, $this->permalink, ...$data );
  }


  private function assert_cannot_access( AccessRedirect $access, ...$data ) {
    wp_set_post_tags( $this->post->ID, $access->get_tag() );
    $redirect = home_url( $access->get_redirect_url() );
    $this->assert_login_and_redirect( $this->permalink, $redirect, ...$data );
  }


  private function assert_login_and_redirect( string $start, string $expected_redirect, ...$data ) {
    $actual_redirect = $this->curl_get_redirect( $start );
    $data[] = [
      'user' => new User( $this->user->ID ),
      'expected' => $expected_redirect,
      'actual' => $actual_redirect,
    ];

    $actual_redirect = trim( $actual_redirect, '/' );
    $expected_redirect = trim( $expected_redirect, '/' );

    $this->assert_equal( $expected_redirect, $actual_redirect, $data );
  }


  private function curl_get_redirect( string $url ): string {
    curl_setopt( $this->curl, CURLOPT_URL, $url );
    curl_setopt( $this->curl, CURLOPT_POST, 0);
    curl_exec( $this->curl );
    $redirected_url = curl_getinfo( $this->curl, CURLINFO_EFFECTIVE_URL );
    return $redirected_url;
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


  private function curl_init() {
    $this->curl = curl_init();
    $login_url = wp_login_url();
    $redirect = home_url( '/' );
    $data = "log={$this->username}&pwd={$this->user_pass}&wp-submit=Log%20In&redirect_to={$redirect}";
    curl_setopt( $this->curl, CURLOPT_POSTFIELDS, $data );
    curl_setopt( $this->curl, CURLOPT_URL, $login_url );
    curl_setopt( $this->curl, CURLOPT_COOKIEJAR, $this->cookie_file );
    curl_setopt( $this->curl, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $this->curl, CURLOPT_USERAGENT, $this->http_agent );
    curl_setopt( $this->curl, CURLOPT_TIMEOUT, 10 );
    curl_setopt( $this->curl, CURLOPT_FOLLOWLOCATION, 1 );
    curl_setopt( $this->curl, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $this->curl, CURLOPT_REFERER, $login_url );
    curl_setopt( $this->curl, CURLOPT_POST, 1);
    curl_exec( $this->curl );
    $this->db_notice( "curl opened" );
  }


  private function curl_close() {
    curl_close( $this->curl );
    $this->db_notice( "curl closed" );
  }


}