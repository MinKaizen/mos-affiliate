<?php declare(strict_types=1);

namespace MOS\Affiliate\ActionHooks;

use \MOS\Affiliate\ActionHook;
use \MGC\Logger\Logger;
use \MOS\Affiliate\DataStructs\ClickbankEvent;
use \MOS\Affiliate\Migration\CommissionsMigration;

class CbSale_UpdateCommissionDb extends ActionHook {

  protected $hook = 'clickbank_event';
  protected $async = true;

  public function handler( $data ): void {
    if ( !( $data instanceof ClickbankEvent ) ) {

    }
    global $wpdb;

    $commission_data = [
      'date' => $data->date,
      'amount' => $data->amount,
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

    $formats = [
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

    
    $table = $wpdb->prefix . CommissionsMigration::TABLE_NAME;
    $result = $wpdb->insert( $table, $commission_data, $formats );
    $this->logger = new Logger( 'mos-affiliate', 'clickbank_event.log' );
    $this->logger->log( print_r( $result, true ) );
  }

}