<?php declare(strict_types=1);

namespace MOS\Affiliate\Adapters;

use \MOS\Affiliate\DataStructs\ClickbankEvent;

class ClickbankEventAdapter extends ClickbankEvent {
  
  public function __construct( string $msg ) {
    $data = json_decode( $msg );
    $this->date = $this->format_date( (string) $data->transactionTime ?? '' );
    $this->amount = $this->calculate_amount( $data );
    $this->product_id = (int) $data->lineItems[0]->itemNo ?? 0;
    $this->product_name = $this->format_product_name( (string) $data->lineItems[0]->productTitle ?? '' );
    $this->transaction_id = (string) $data->receipt ?? '';
    $this->transaction_type = (string) $data->transactionType ?? '';
    $this->cb_affiliate = (string) $data->affiliate ?? '';
    $this->customer_wpid = (int) $data->vendorVariables->customer_wpid ?? 0;
    $this->customer_username = (string) $data->vendorVariables->customer_username ?? '';
    $this->customer_name = (string) $data->vendorVariables->customer_name ?? '';
    $this->customer_email = (string) $data->vendorVariables->customer_email ?? '';
    $this->sponsor_wpid = (int) $data->vendorVariables->sponsor_wpid ?? 0;
    $this->sponsor_username = (string) $data->vendorVariables->sponsor_username ?? '';
    $this->sponsor_name = (string) $data->vendorVariables->sponsor_name ?? '';
    $this->sponsor_email = (string) $data->vendorVariables->sponsor_email ?? '';
    $this->campaign = (string) $data->vendorVariables->campaign ?? '';
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