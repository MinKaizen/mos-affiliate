<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

class AccessRedirectActionHookTest extends Test {

  // see Products config
  const USERMETA_KEYS = [
    'monthly_partner' => 'mos_access_monthly_partner',
    'fb_toolkit' => 'mos_access_fb_toolkit',
    'authority_bonuses' => 'mos_access_authority_bonuses',
    'lead_system' => 'mos_access_lead_system',
    'yearly_partner' => 'mos_access_yearly_partner',
    'lifetime_partner' => 'mos_access_lifetime_partner',
    'coaching' => 'mos_access_coaching',
  ];

  // see ACF config
  const POSTMETA = [
    'key' => 'access_level',
    'acf_key' => '_access_level',
    'acf_value' => 'field_5fe255ff02d8d',
  ];

  // see Products config
  const REDIRECTS = [
    'monthly_partner' => '/no-access-monthly-partner',
    'fb_toolkit' => '/no-access-fb-toolkit',
    'authority_bonuses' => '/no-access-authority-bonuses',
    'lead_system' => '/no-access-lead-system',
    'yearly_partner' => '/no-access-yearly-partner',
    'lifetime_partner' => '/no-access-lifetime-partner',
    'coaching' => '/no-access-coaching',
  ];

  private $post;

  public function _before(): void {
    $this->_injected_user = $this->create_test_user();
    $this->set_user();
    $this->post = $this->create_test_post();
  }

  public function test_all(): void {
    // $post_path = '/' . str_replace( ' ', '-', strtolower( $this->post->post_name ) );
    // $res = wp_remote_get( home_url( '/wp-login.php?' ) )['http_response']->get_response_object() ?? null;
    // print_r( get_object_vars( $res ) );

    // $this->assert_redirect( $post_path, $post_path, ['msg' => 'URL should not redirect by default'] );
    // foreach ( array_keys( self::USERMETA_KEYS ) as $access_level ) {
    //   $this->post_set_access_level( $this->post->ID, $access_level );
    //   $this->assert_redirect( $post_path, self::REDIRECTS[$access_level], [
    //     'msg' => 'User should be redirected after page access level is set',
    //     'access_level' => $access_level
    //   ] );
    //   $this->user_give_access( $this->_injected_user->ID, $access_level );
    //   $this->assert_redirect( $post_path, $post_path, [
    //     'msg' => 'User should be able to access page after access is given',
    //     'access_level' => $access_level
    //   ] );
    // }
  }

  private function post_set_access_level( int $post_id, string $access_level ): void {
    \update_post_meta( $post_id, self::POSTMETA['key'], $access_level );
    \update_post_meta( $post_id, self::POSTMETA['acf_key'], self::POSTMETA['acf_value'] );
  }

  private function user_give_access( int $user_id, string $access_level ): void {
    $tomorrow = \date( 'Y-m-d', \time() + \DAY_IN_SECONDS );
    \update_user_meta( $user_id, self::USERMETA_KEYS[$access_level], $tomorrow );
  }


  private function assert_login_and_redirect( string $start, string $expected_redirect, ...$data ) {
    $actual_redirect = $this->curl_get_redirect( $start );
    $data[] = [
      'expected' => $expected_redirect,
      'actual' => $actual_redirect,
    ];

    $actual_redirect = trim( $actual_redirect, '/' );
    $expected_redirect = trim( $expected_redirect, '/' );
    $condition = $expected_redirect == $actual_redirect;
    $assertion = __FUNCTION__;
    $this->assert( $condition, $data, $assertion );
  }


  private function curl_get_redirect( string $url ): string {
    $this->db_notice( 'CURL: ' . $url );
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