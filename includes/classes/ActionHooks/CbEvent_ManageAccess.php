<?php declare(strict_types=1);

namespace MOS\Affiliate\ActionHooks;

use \MOS\Affiliate\ActionHook;
use \MOS\Affiliate\Product;
use \MOS\Affiliate\User;
use \MOS\Affiliate\DataStructs\ClickbankEvent;

class CbEvent_ManageAccess extends ActionHook {

  const GRANT_ACCESS_TRANSACTION_TYPES = ['SALE', 'BILL', 'TEST_SALE', 'TEST_BILL'];
  const REMOVE_ACCESS_TRANSACTION_TYPES = ['RFND', 'CGBK', 'TEST_RFND', 'TEST_CGBK'];
  const NO_DATE = '0000-01-01';
  const EVERGREEN_DATE = '9999-01-01';
  const DATE_FORMAT = 'Y-m-d';

  protected $hook = 'clickbank_event';

  public function handler( $data ): void {
    if ( !( $data instanceof ClickbankEvent ) ) {
      return;
    }

    $product = Product::from_cb_id( $data->product_id );
    if ( !$product->exists ) {
      return;
    }
    
    $user = User::from_id( $data->customer_wpid );
    if ( !$user->exists() ) {
      return;
    }
    
    if ( in_array( $data->transaction_type, self::GRANT_ACCESS_TRANSACTION_TYPES ) ) {
      $this->grant_access( $user->get_wpid(), $product, $data->transaction_type );
    } elseif ( in_array( $data->transaction_type, self::REMOVE_ACCESS_TRANSACTION_TYPES ) ) {
      $this->remove_access( $user->get_wpid(), $product );
    }
  }
  
  private function grant_access( int $user_id, Product $product, string $transaction_type ): void {
    $access_date = $this->access_date( $product, $transaction_type );
    $meta_key = $product->access_meta_key;
    \update_user_meta( $user_id, $meta_key, $access_date );
  }

  private function remove_access( int $user_id, Product $product ): void {
    $meta_key = $product->access_meta_key;
    \delete_user_meta( $user_id, $meta_key );
  }

  private function access_date( Product $product, string $transaction_type ): string {
    if ( $transaction_type == 'SALE' && !$product->is_recurring ) {
      $access_date = self::EVERGREEN_DATE;
    } elseif ( $transaction_type == 'SALE' ) {
      $access_period = $product->has_trial_period ? $product->trial_period : $product->rebill_period;
      $access_date = date( self::DATE_FORMAT, time() + \DAY_IN_SECONDS * $access_period );
    } elseif ( $transaction_type == 'BILL' ) {
      $access_period = $product->rebill_period;
      $access_date = date( self::DATE_FORMAT, time() + \DAY_IN_SECONDS * $access_period );
    } else {
      $access_date = self::NO_DATE;
    }

    return $access_date;
  }

}