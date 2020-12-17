<?php declare(strict_types=1);

namespace MOS\Affiliate\Routes;

use \MOS\Affiliate\Adapters\ClickbankEventAdapter;
use \MGC\REST_Route\REST_Route;
use \WP_REST_Request;
use \WP_REST_Response;

class ClickbankEndpoint extends REST_Route {

  const SECRET_KEY = 'ANTOLAMAS61412';
  
  protected $root = 'mos-affiliate/v1';
  protected $route = 'clickbank-ins';
  protected $method = 'POST';

  public function handler( WP_REST_Request $request ): WP_REST_Response {
    $data = new ClickbankEventAdapter( $request->get_body() );
    return $this->response( $data );
  }

  public function permission(): bool {
    return true;
  }

  // Missing type hint. Could return array or object depending on json_decode()
  private function adapter( WP_REST_Request $request ) {
    $request_body = json_decode( $request->get_body() );
    $data_encrypted = $request_body->notification;
    $iv = $request_body->iv;
    $data = json_decode( $this->decrypt_cb_notification( $data_encrypted, $iv, self::SECRET_KEY ) );
    return $data;
  }

  private function decrypt_cb_notification( string $notification, string $iv, string $secret_key ): string {
    $decrypted = trim(
      openssl_decrypt(base64_decode($notification),
      'AES-256-CBC',
      substr(sha1($secret_key), 0, 32),
      OPENSSL_RAW_DATA,
      base64_decode($iv)), "\0..\32"
    );
    return $decrypted;
  }

}