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

  public function test_update_commissions_on_sale(): void {
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

    \do_action( 'clickbank_event', $sale );

     // Call cron so that our async hook gets called
    \wp_remote_get( \home_url( '/wp-cron.php' ) );

    // Give it some time to resolve
    sleep( self::ASYNC_BUFFER );

    global $wpdb;
    $table = $wpdb->prefix . CommissionsMigration::TABLE_NAME;
    $conditions = [
      "actor_id = $sale->customer_wpid",
      "earner_id = $sale->sponsor_wpid",
      "transaction_id = '$sale->transaction_id'",
      "campaign = '$sale->campaign'",
    ];
    $query = "SELECT * FROM $table WHERE " . implode( ' AND ', $conditions );
    $commission = $wpdb->get_row( $query, 'OBJECT' );

    $this->assert_not_empty( $commission, $query );
    $this->assert_equal( $commission->date, $sale->date);
    $this->assert_equal( $commission->amount, $sale->amount);
    $this->assert_equal( $commission->description, $sale->product_name);
    $this->assert_equal( $commission->transaction_id, $sale->transaction_id);
    $this->assert_equal( $commission->campaign, $sale->campaign);
    $this->assert_equal( $commission->actor_id, $sale->customer_wpid);
    $this->assert_equal( $commission->earner_id, $sale->sponsor_wpid);
    $this->assert_equal( $commission->payout_date, $sale->date);
    $this->assert_equal( $commission->payout_method, 'Clickbank');
    $this->assert_equal( $commission->payout_address, $sale->cb_affiliate);
    $this->assert_equal( $commission->payout_transaction_id, $sale->transaction_id);
  }

}