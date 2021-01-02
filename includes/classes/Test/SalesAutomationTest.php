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
    $sale = new ClickbankEvent();
    $sale->date = "2021-01-01";
    $sale->amount = 197;
    $sale->product_id = 100;
    $sale->product_name = self::TEST_COMMISSION_DESCRIPTION;
    $sale->transaction_id = ranstr(32);
    $sale->transaction_type = "SALE";
    $sale->cb_affiliate = ranstr(6);
    $sale->campaign = ranstr(6);
    $sale->customer_wpid = $this->user->get_wpid();
    $sale->customer_username = $this->user->get_username();
    $sale->customer_name = $this->user->get_name();
    $sale->customer_email = $this->user->get_email();
    $sale->sponsor_wpid = $this->sponsor->get_wpid();
    $sale->sponsor_username = $this->sponsor->get_username();
    $sale->sponsor_name = $this->sponsor->get_name();
    $sale->sponsor_email = $this->sponsor->get_email();

    $refund = new ClickbankEvent();
    $refund->date = "2021-01-01";
    $refund->amount = -200; // Note it's slightly higher than the sale
    $refund->product_id = 100;
    $refund->product_name = self::TEST_COMMISSION_DESCRIPTION;
    $refund->transaction_id = $sale->transaction_id; // Note same as sale
    $refund->transaction_type = "RFND";
    $refund->cb_affiliate = ranstr(6);
    $refund->campaign = ranstr(6);
    $refund->customer_wpid = $this->user->get_wpid();
    $refund->customer_username = $this->user->get_username();
    $refund->customer_name = $this->user->get_name();
    $refund->customer_email = $this->user->get_email();
    $refund->sponsor_wpid = $this->sponsor->get_wpid();
    $refund->sponsor_username = $this->sponsor->get_username();
    $refund->sponsor_name = $this->sponsor->get_name();
    $refund->sponsor_email = $this->sponsor->get_email();

    $error_refund = new ClickbankEvent();
    $error_refund->date = "2021-01-01";
    $error_refund->amount = -200;
    $error_refund->product_id = 100;
    $error_refund->product_name = self::TEST_COMMISSION_DESCRIPTION;
    $error_refund->transaction_id = ranstr(10);
    $error_refund->transaction_type = "RFND";
    $error_refund->cb_affiliate = ranstr(6);
    $error_refund->campaign = ranstr(6);
    $error_refund->customer_wpid = $this->user->get_wpid();
    $error_refund->customer_username = $this->user->get_username();
    $error_refund->customer_name = $this->user->get_name();
    $error_refund->customer_email = $this->user->get_email();
    $error_refund->sponsor_wpid = $this->sponsor->get_wpid();
    $error_refund->sponsor_username = $this->sponsor->get_username();
    $error_refund->sponsor_name = $this->sponsor->get_name();
    $error_refund->sponsor_email = $this->sponsor->get_email();

    \do_action( 'clickbank_event', $sale );
    \do_action( 'clickbank_event', $refund );
    \do_action( 'clickbank_event', $error_refund );

     // Call cron so that our async hook gets called
    \wp_remote_get( \home_url( '/wp-cron.php' ) );

    // Give it some time to resolve
    sleep( self::ASYNC_BUFFER );

    global $wpdb;
    $table = $wpdb->prefix . CommissionsMigration::TABLE_NAME;
    $sale_conditions = [
      "actor_id = $sale->customer_wpid",
      "earner_id = $sale->sponsor_wpid",
      "transaction_id = '$sale->transaction_id'",
      "campaign = '$sale->campaign'",
    ];
    $query = "SELECT * FROM $table WHERE " . implode( ' AND ', $sale_conditions );
    $db_sale = $wpdb->get_row( $query, 'OBJECT' );

    $refund_conditions = [
      "actor_id = $refund->customer_wpid",
      "earner_id = $refund->sponsor_wpid",
      "transaction_id = '$refund->transaction_id'",
      "campaign = '$refund->campaign'",
    ];
    $query = "SELECT * FROM $table WHERE " . implode( ' AND ', $refund_conditions );
    $db_refund = $wpdb->get_row( $query, 'OBJECT' );

    $error_refund_conditions = [
      "actor_id = $error_refund->customer_wpid",
      "earner_id = $error_refund->sponsor_wpid",
      "transaction_id = '$error_refund->transaction_id'",
      "campaign = '$error_refund->campaign'",
    ];
    $query = "SELECT * FROM $table WHERE " . implode( ' AND ', $error_refund_conditions );
    $db_error_refund = $wpdb->get_row( $query, 'OBJECT' );

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