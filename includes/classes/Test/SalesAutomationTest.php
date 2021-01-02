<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use MOS\Affiliate\DataStructs\ClickbankEvent;
use MOS\Affiliate\Test;
use MOS\Affiliate\Migration\CommissionsMigration;
use function MOS\Affiliate\ranstr;

class SalesAutomationTest extends Test {

  const ASYNC_BUFFER = 1;

  private $user;
  private $sponsor;

  public function _before(): void {
    $this->user = $this->create_test_user();
    $this->sponsor = $this->create_test_user();
  }

  public function test_update_commissions(): void {
    $sale = $this->create_test_cb_event([
      'transaction_type' => 'SALE',
    ]);

    $refund = $this->create_test_cb_event([
      'transaction_type' => 'RFND',
      'amount' => -($sale->amount + 10),
      'transaction_id' => $sale->transaction_id,
    ]);

    $error_refund = $this->create_test_cb_event([
      'transaction_type' => 'RFND',
    ]);

    \do_action( 'clickbank_event', $sale );
    \do_action( 'clickbank_event', $refund );
    \do_action( 'clickbank_event', $error_refund );

     // Call cron so that our async hook gets called
    \wp_remote_get( \home_url( '/wp-cron.php' ) );

    // Give it some time to resolve
    sleep( self::ASYNC_BUFFER );

    $db_sale = $this->find_commission( $sale );
    $db_refund = $this->find_commission( $refund );
    $db_error_refund = $this->find_commission( $error_refund );

    $this->assert_not_empty( $db_sale );
    $this->assert_equal( $db_sale->date, $sale->date);
    $this->assert_equal( $db_sale->amount, $sale->amount);
    $this->assert_equal( $db_sale->description, $sale->product_name);
    $this->assert_equal( $db_sale->transaction_id, $sale->transaction_id);
    $this->assert_equal( $db_sale->campaign, $sale->campaign);
    $this->assert_equal( $db_sale->actor_id, $sale->customer_wpid);
    $this->assert_equal( $db_sale->earner_id, $sale->sponsor_wpid);
    $this->assert_equal( $db_sale->payout_date, $sale->date);
    $this->assert_equal( $db_sale->payout_method, 'Clickbank');
    $this->assert_equal( $db_sale->payout_address, $sale->cb_affiliate);
    $this->assert_equal( $db_sale->payout_transaction_id, $sale->transaction_id);

    $this->assert_not_empty( $db_refund );
    $this->assert_equal( $db_refund->date, $refund->date);
    $this->assert_equal( $db_refund->amount, -$sale->amount);
    $this->assert_equal( $db_refund->description, $refund->product_name);
    $this->assert_equal( $db_refund->transaction_id, $refund->transaction_id);
    $this->assert_equal( $db_refund->campaign, $refund->campaign);
    $this->assert_equal( $db_refund->actor_id, $refund->customer_wpid);
    $this->assert_equal( $db_refund->earner_id, $refund->sponsor_wpid);
    $this->assert_equal( $db_refund->payout_date, $refund->date);
    $this->assert_equal( $db_refund->payout_method, 'Clickbank');
    $this->assert_equal( $db_refund->payout_address, $refund->cb_affiliate);
    $this->assert_equal( $db_refund->payout_transaction_id, $refund->transaction_id);

    $this->assert_empty( $db_error_refund );
  }

  private function find_commission( ClickbankEvent $event_data ): ?object {
    global $wpdb;
    $table = $wpdb->prefix . CommissionsMigration::TABLE_NAME;
    $conditions = [
      "actor_id = $event_data->customer_wpid",
      "earner_id = $event_data->sponsor_wpid",
      "transaction_id = '$event_data->transaction_id'",
      "campaign = '$event_data->campaign'",
    ];
    $query = "SELECT * FROM $table WHERE " . implode( ' AND ', $conditions );
    $commission = $wpdb->get_row( $query, 'OBJECT' );
    $commission = $commission ? $commission : null;
    return $commission;
  }

  private function create_test_cb_event( array $args = [] ): ClickbankEvent {
    $default_args = [
      'amount' => rand(10, 997),
      'product_id' => rand(1, 100),
      'transaction_id' => ranstr(32),
      'cb_affiliate' => ranstr(6),
      'campaign' => ranstr(6),
      'customer_wpid' => $this->user->get_wpid(),
      'customer_username' => $this->user->get_username(),
      'customer_name' => $this->user->get_name(),
      'customer_email' => $this->user->get_email(),
      'sponsor_wpid' => $this->sponsor->get_wpid(),
      'sponsor_username' => $this->sponsor->get_username(),
      'sponsor_name' => $this->sponsor->get_name(),
      'sponsor_email' => $this->sponsor->get_email(),
    ];

    $mandatory_args = [
      'product_name' => self::TEST_COMMISSION_DESCRIPTION,
    ];

    $merged = array_replace( $default_args, $args, $mandatory_args );
    $cb_event = new ClickbankEvent();

    foreach ( $merged as $key => $value ) {
      if ( property_exists( $cb_event, $key ) ) {
        $cb_event->$key = $value;
      }
    }

    return $cb_event;
  }

}