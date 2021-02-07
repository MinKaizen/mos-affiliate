<?php declare(strict_types=1);

namespace MOS\Affiliate;

class Access {

  const NO_DATE = '0000-01-01';
  const EVERGREEN_DATE = '9999-01-01';
  const DATE_FORMAT = 'Y-m-d';

  const SALE_TRANSACTIONS = ['SALE', 'TEST_SALE'];
  const REBILL_TRANSACTIONS = ['BILL', 'TEST_BILL'];
  const REFUND_TRANSACTION = ['RFND', 'TEST_RFND', 'CGBK', 'TEST_CGBK'];

  const TXN_SALE = 'SALE';
  const TXN_TEST_SALE = 'TEST_SALE';
  const TXN_BILL = 'BILL';
  const TXN_TEST_BILL = 'TEST_BILL';
  const TXN_RFND = 'RFND';
  const TXN_TEST_RFND = 'TEST_RFND';
  const TXN_CGBK = 'CGBK';
  const TXN_TEST_CGBK = 'TEST_CGBK';

  public static function grant_access( int $user_id, Product $product, string $transaction_type ): void {
    $access_date = self::access_date( $product, $transaction_type );
    $meta_key = $product->access_meta_key;
    \update_user_meta( $user_id, $meta_key, $access_date );
  }

  public static function remove_access( int $user_id, Product $product ): void {
    $meta_key = $product->access_meta_key;
    \delete_user_meta( $user_id, $meta_key );
  }

  public static function access_date( Product $product, string $transaction_type ): string {
    if ( in_array( $transaction_type, self::SALE_TRANSACTIONS ) && !$product->is_recurring ) {
      $access_date = self::EVERGREEN_DATE;
    } elseif ( in_array( $transaction_type, self::SALE_TRANSACTIONS ) ) {
      $access_period = $product->has_trial_period ? $product->trial_access_duration : $product->rebill_access_duration;
      $access_date = date( self::DATE_FORMAT, time() + \DAY_IN_SECONDS * $access_period );
    } elseif ( in_array( $transaction_type, self::REBILL_TRANSACTIONS ) ) {
      $access_period = $product->rebill_access_duration;
      $access_date = date( self::DATE_FORMAT, time() + \DAY_IN_SECONDS * $access_period );
    } else {
      $access_date = self::NO_DATE;
    }

    return $access_date;
  }

}