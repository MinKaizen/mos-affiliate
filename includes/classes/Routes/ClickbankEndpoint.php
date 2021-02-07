<?php declare(strict_types=1);

namespace MOS\Affiliate\Routes;

use \MOS\Affiliate\Adapters\ClickbankEventAdapter;
use \MGC\REST_Route\REST_Route;
use \WP_REST_Request;
use \WP_REST_Response;

class ClickbankEndpoint extends REST_Route {

  private $secret_key = 'ANTOLAMAS61412';
  
  protected $root = 'mos-affiliate/v1';
  protected $route = 'clickbank-ins';
  protected $method = 'POST';

  public function handler( WP_REST_Request $request ): WP_REST_Response {
    $body = $request->get_body();
    if ( $this->is_encrypted( $body ) ) {
      $original_json = $this->extract_decrypted_msg( $body );
    } else {
      $original_json = $body;
    }
    $original_object = json_decode( $original_json );
    $adapted_object = new ClickbankEventAdapter( $original_json );
    \do_action( 'clickbank_event_raw', $original_object );
    \do_action( 'clickbank_event', $adapted_object );
    return $this->response( $original_object );
    // return $this->response( $adapted_object );
  }

  private function is_encrypted( string $json ): bool {
    $data = json_decode( $json );
    $encrypted = ( isset( $data->iv ) && isset( $data->notification ) && is_string( $data->notification ) );
    return $encrypted;
  }

  private function extract_decrypted_msg( string $json ): string {
    $data = json_decode( $json );
    $msg_encrypted = $data->notification;
    $iv = $data->iv;
    $msg_decrypted = $this->decrypt( $msg_encrypted, $iv, $this->secret_key );
    return $msg_decrypted;
  }

  private function decrypt( string $notification, string $iv, string $secret_key ): string {
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