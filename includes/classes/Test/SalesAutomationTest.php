<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use MOS\Affiliate\DataStructs\ClickbankEvent;
use MOS\Affiliate\Test;
use MOS\Affiliate\Migration\CommissionsMigration;
use function MOS\Affiliate\ranstr;

class SalesAutomationTest extends Test {

  private $user;
  private $sponsor;

  public function _before(): void {
    $this->user = $this->create_test_user();
    $this->sponsor = $this->create_test_user();
  }

  public function test_update_commissions_on_sale(): void {
    $data = new ClickbankEvent();
    $data->date = "2021-01-01";
    $data->amount = 197;
    $data->product_id = 100;
    $data->product_name = self::TEST_COMMISSION_DESCRIPTION;
    $data->transaction_id = ranstr(32);
    $data->transaction_type = "SALE";
    $data->cb_affiliate = ranstr(6);
    $data->campaign = ranstr(6);
    $data->customer_wpid = $this->user->get_wpid();
    $data->customer_username = $this->user->get_username();
    $data->customer_name = $this->user->get_name();
    $data->customer_email = $this->user->get_email();
    $data->sponsor_wpid = $this->sponsor->get_wpid();
    $data->sponsor_username = $this->sponsor->get_username();
    $data->sponsor_name = $this->sponsor->get_name();
    $data->sponsor_email = $this->sponsor->get_email();

    \do_action( 'clickbank_event', $data );

     // Call cron so that our async hook gets called
    \wp_remote_get( \home_url( '/wp-cron.php' ) );

    // Give it some time to resolve
    sleep(1);

    global $wpdb;
    $table = $wpdb->prefix . CommissionsMigration::TABLE_NAME;
    $conditions = [
      "actor_id = $data->customer_wpid",
      "earner_id = $data->sponsor_wpid",
      "transaction_id = '$data->transaction_id'",
      "campaign = '$data->campaign'",
    ];
    $query = "SELECT * FROM $table WHERE " . implode( ' AND ', $conditions );
    $commission = $wpdb->get_row( $query, 'OBJECT' );

    $this->assert_not_empty( $commission, $query );
    $this->assert_equal( $commission->date, $data->date);
    $this->assert_equal( $commission->amount, $data->amount);
    $this->assert_equal( $commission->description, $data->product_name);
    $this->assert_equal( $commission->transaction_id, $data->transaction_id);
    $this->assert_equal( $commission->campaign, $data->campaign);
    $this->assert_equal( $commission->actor_id, $data->customer_wpid);
    $this->assert_equal( $commission->earner_id, $data->sponsor_wpid);
    $this->assert_equal( $commission->payout_date, $data->date);
    $this->assert_equal( $commission->payout_method, 'Clickbank');
    $this->assert_equal( $commission->payout_address, $data->cb_affiliate);
    $this->assert_equal( $commission->payout_transaction_id, $data->transaction_id);
  }

}