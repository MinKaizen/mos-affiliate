<?php declare(strict_types=1);

namespace MOS\Affiliate\Adapters;

use \MOS\Affiliate\DataStructs\ClickbankEvent;

class ClickbankEventAdapter extends ClickbankEvent {
  
  private $secret_key = 'ANTOLAMAS61412';
  private $original;

  public function __construct( string $body_json ) {
    $body = json_decode( $body_json );
    $msg_encrypted = $body->notification;
    $iv = $body->iv;
    $data = json_decode( $this->decrypt_cb_notification( $msg_encrypted, $iv, $this->secret_key ) );
    
    $this->original = $data;

    // Test data
    // $data = json_decode( \file_get_contents( __DIR__ . '/clickbank_refund.json' ) );

    $this->date = $this->format_date( $data->transactionTime );
    $this->amount = $this->calculate_amount( $data );
    $this->product_id = isset($data->lineItems[0]->itemNo) ? (int) $data->lineItems[0]->itemNo : $this->product_id;
    $this->product_name = isset( $data->lineItems[0]->productTitle ) ? $this->format_product_name( $data->lineItems[0]->productTitle ) : $this->product_name;
    $this->transaction_id = isset( $data->receipt ) ? (string) $data->receipt : $this->transaction_id;
    $this->transaction_type = isset( $data->transactionType ) ? (string) $data->transactionType : $this->transaction_type;
    $this->cb_affiliate = isset( $data->affiliate ) ? (string) $data->affiliate : $this->cb_affiliate;
    $this->customer_wpid = isset( $data->vendorVariables->customer_wpid ) ? (int) $data->vendorVariables->customer_wpid : $this->customer_wpid;
    $this->customer_username = isset( $data->vendorVariables->customer_username ) ? (string) $data->vendorVariables->customer_username : $this->customer_username;
    $this->customer_name = isset( $data->vendorVariables->customer_name ) ? (string) $data->vendorVariables->customer_name : $this->customer_name;
    $this->customer_email = isset( $data->vendorVariables->customer_email ) ? (string) $data->vendorVariables->customer_email : $this->customer_email;
    $this->sponsor_wpid = isset( $data->vendorVariables->sponsor_wpid ) ? (int) $data->vendorVariables->sponsor_wpid : $this->sponsor_wpid;
    $this->sponsor_username = isset( $data->vendorVariables->sponsor_username ) ? (string) $data->vendorVariables->sponsor_username : $this->sponsor_username;
    $this->sponsor_name = isset( $data->vendorVariables->sponsor_name ) ? (string) $data->vendorVariables->sponsor_name : $this->sponsor_name;
    $this->sponsor_email = isset( $data->vendorVariables->sponsor_email ) ? (string) $data->vendorVariables->sponsor_email : $this->sponsor_email;
    $this->campaign = isset( $data->vendorVariables->campaign ) ? (string) $data->vendorVariables->campaign : $this->campaign;
  }

  // No type hint. Could be array 
  public function get_original() {
    return $this->original;
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

  private function format_product_name( string $name ): string {
    return trim( str_replace( 'My Online Startup', '', $name ) );
  }

  private function format_date( string $iso_datetime ): string {
    $datetime = new \DateTime( $iso_datetime );
    $formatted_date = (string) $datetime->format( 'Y-m-d' );
    return $formatted_date;
  }

  private function calculate_amount( object $cb_notification ): float {
    $amount = 0.0;
    if ( isset( $cb_notification->lineItems[0]->productPrice ) ) {
      $amount = $cb_notification->lineItems[0]->productPrice;
    } elseif ( isset( $cb_notification->totalOrderAmount ) && isset( $cb_notification->totalTaxAmount ) ) {
      $amount_inc_tax = (float) $cb_notification->totalOrderAmount;
      $tax = (float) $cb_notification->totalTaxAmount;
      $amount = $amount_inc_tax - $tax;
    }
    return $amount;
  }

}