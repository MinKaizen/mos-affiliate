<?php declare(strict_types=1);

namespace MOS\Affiliate\Actions;

use \MOS\Affiliate\AbstractAction;
use \MGC\Logger\Logger;
use \MOS\Affiliate\DataStructs\ClickbankEvent;
use \MOS\Affiliate\Migration\CommissionsMigration;

class CbEvent_UpdateCommissions extends AbstractAction {

  const ADD_COMMISSION_TRANSACTION_TYPES = ['SALE', 'BILL', 'TEST_SALE', 'TEST_BILL'];
  const ADD_REFUND_TRANSACTION_TYPES = ['RFND', 'CGBK', 'TEST_RFND', 'TEST_CGBK'];
  const FORMATS = [
    'date' =>'%s',
    'amount' =>'%f',
    'description' =>'%s',
    'transaction_id' =>'%s',
    'campaign' =>'%s',
    'actor_id' =>'%d',
    'earner_id' =>'%d',
    'payout_date' =>'%s',
    'payout_method' =>'%s',
    'payout_address' =>'%s',
    'payout_transaction_id' =>'%s',
  ];

  protected $hook = 'clickbank_event';

  public function __construct() {
    $this->logger = new Logger( 'mos-affiliate', 'clickbank_error.log' );
  }

  public function handler( $data ): void {
    if ( !( $data instanceof ClickbankEvent ) ) {
      return;
    }

    if ( in_array( $data->transaction_type, self::ADD_COMMISSION_TRANSACTION_TYPES ) ) {
      $this->add_commission( $data );
    } elseif ( in_array( $data->transaction_type, self::ADD_REFUND_TRANSACTION_TYPES ) ) {
      $this->add_refund( $data );
    }
  }

  private function add_commission( ClickbankEvent $data ): void {
    global $wpdb;

    $commission_data = [
      'date' => $data->date,
      'amount' => $data->commission,
      'description' => $data->product_name,
      'transaction_id' => $data->transaction_id,
      'campaign' => $data->campaign,
      'actor_id' => $data->customer_wpid,
      'earner_id' => $data->sponsor_wpid,
      'payout_date' => $data->date,
      'payout_method' => 'Clickbank',
      'payout_address' => $data->cb_affiliate,
      'payout_transaction_id' => $data->transaction_id,
    ];

    $table = $wpdb->prefix . CommissionsMigration::TABLE_NAME;
    $wpdb->insert( $table, $commission_data, self::FORMATS );
  }

  private function add_refund( ClickbankEvent $data ): void {
    global $wpdb;
    $table = $wpdb->prefix . CommissionsMigration::TABLE_NAME;
    $query = "SELECT amount
              FROM $table
              WHERE transaction_id = '$data->transaction_id'
              AND amount > 0
              AND earner_id = $data->sponsor_wpid
              AND actor_id = $data->customer_wpid";
    $commission_amount = $wpdb->get_var( $query );

    if ( empty( $commission_amount ) ) {
      $this->logger->log( "Could not find matching commission. No refund was created", ['REFUND', $data->transaction_id] );
      $this->logger->log( json_encode( $data ), ['REFUND', $data->transaction_id] );
      return;
    }

    $refund_data = [
      'date' => $data->date,
      'amount' => -1.0 * (float) $commission_amount,
      'description' => $data->product_name,
      'transaction_id' => $data->transaction_id,
      'campaign' => $data->campaign,
      'actor_id' => $data->customer_wpid,
      'earner_id' => $data->sponsor_wpid,
      'payout_date' => $data->date,
      'payout_method' => 'Clickbank',
      'payout_address' => $data->cb_affiliate,
      'payout_transaction_id' => $data->transaction_id,
    ];

    $wpdb->insert( $table, $refund_data, self::FORMATS );    
  }

}