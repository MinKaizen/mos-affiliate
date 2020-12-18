<?php declare(strict_types=1);

namespace MOS\Affiliate\DataStructs;

class ClickbankEvent {
  public $date = '0000-00-00';
  public $amount = 0.0;
  public $product_id = 0;
  public $product_name = '';
  public $transaction_id = '';
  public $transaction_type = '';
  public $cb_affiliate = '';
  public $campaign = '';

  public $customer_wpid = 0;
  public $customer_username = '';
  public $customer_name = '';
  public $customer_email = '';

  public $sponsor_wpid = 0;
  public $sponsor_username = '';
  public $sponsor_name = '';
  public $sponsor_email = '';

  public function object(): object {
    return $this;
  }

  public function json(): string {
    return json_encode( $this );
  }

  public function array(): array {
    return (array) $this;
  }
}