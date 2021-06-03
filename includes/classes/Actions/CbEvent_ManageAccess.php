<?php declare(strict_types=1);

namespace MOS\Affiliate\Actions;

use \MOS\Affiliate\AbstractAction;
use \MOS\Affiliate\Product;
use \MOS\Affiliate\User;
use \MOS\Affiliate\Access;
use \MOS\Affiliate\DataStructs\ClickbankEvent;

class CbEvent_ManageAccess extends AbstractAction {

  const GRANT_ACCESS_TRANSACTION_TYPES = ['SALE', 'BILL', 'TEST_SALE', 'TEST_BILL'];
  const REMOVE_ACCESS_TRANSACTION_TYPES = ['RFND', 'CGBK', 'TEST_RFND', 'TEST_CGBK'];

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
      Access::grant_access( $user->get_wpid(), $product, $data->transaction_type );
    } elseif ( in_array( $data->transaction_type, self::REMOVE_ACCESS_TRANSACTION_TYPES ) ) {
      Access::remove_access( $user->get_wpid(), $product );
    }
  }

}