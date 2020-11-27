<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\AccessRedirect;
use \MOS\Affiliate\Test;
use \WP_CLI;
use \MOS\Affiliate\AccessRedirect\FreeAccessRedirect;
use \MOS\Affiliate\AccessRedirect\MonthlyPartnerAccessRedirect;
use \MOS\Affiliate\AccessRedirect\YearlyPartnerAccessRedirect;

use function \wp_set_post_tags;
use function \home_url;
use function \wp_get_upload_dir;
use function \wp_login_url;

class AccessRedirectsTest extends Test {

  private $username = '3TQSX6qfj22oX7tgB5zIpV3RPZePfDAA';
  private $user_pass = '5FwZsUZ8IFJ60ofVz2rgftHxDcvcrQXb';
  private $post;
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

    $this->_injected_user = $this->create_test_user( [
      'user_login' => $this->username,
      'user_pass' => $this->user_pass,
    ] );
    
    $this->post = $this->create_test_post();
    $this->permalink = get_permalink( $this->post->ID );
    $this->curl_init();
  }


  public function __destruct() {
    $this->curl_close();
  }


  public function test_free_member(): void {
    $this->_injected_user->set_role('free');
    
    $this->assert_can_access( $this->accesses['free'] );
    $this->assert_cannot_access( $this->accesses['monthly_partner'] );
    $this->assert_cannot_access( $this->accesses['yearly_partner'] );
  }


  public function test_monthly_partner(): void {
    $this->_injected_user->set_role('monthly_partner');
    
    $this->assert_can_access( $this->accesses['free'] );
    $this->assert_can_access( $this->accesses['monthly_partner'] );
    $this->assert_cannot_access( $this->accesses['yearly_partner'] );
  }


  public function test_yearly_partner(): void {
    $this->_injected_user->set_role('yearly_partner');
    
    $this->assert_can_access( $this->accesses['free'] );
    $this->assert_can_access( $this->accesses['monthly_partner'] );
    $this->assert_can_access( $this->accesses['yearly_partner'] );
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
      'user' => $this->_injected_user,
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