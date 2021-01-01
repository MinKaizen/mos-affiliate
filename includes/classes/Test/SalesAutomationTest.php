<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

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
    $data = [
      "date" => "2021-01-01",
      "amount" => 197,
      "product_id" => 100,
      "product_name" => "mos_affiliate_test",
      "transaction_id" => ranstr(32),
      "transaction_type" => "SALE",
      "cb_affiliate" => "mos_affiliate_test",
      "campaign" => ranstr(6),
      "customer_wpid" => $this->user->get_wpid(),
      "customer_username" => $this->user->get_username(),
      "customer_name" => $this->user->get_name(),
      "customer_email" => $this->user->get_email(),
      "sponsor_wpid" => $this->sponsor->get_wpid(),
      "sponsor_username" => $this->sponsor->get_username(),
      "sponsor_name" => $this->sponsor->get_name(),
      "sponsor_email" => $this->sponsor->get_email(),
    ];

    \do_action( 'clickbank_event', $data );

    global $wpdb;
    $table = $wpdb->prefix . CommissionsMigration::TABLE_NAME;
    $conditions = [
      "actor_id = $data[customer_wpid]",
      "earner_id = $data[sponsor_wpid]",
      "transaction_id = '$data[transaction_id]'",
      "campaign = '$data[campaign]'",
    ];
    $query = "SELECT * FROM $table WHERE " . implode( ' AND ', $conditions );
    $commission = $wpdb->get_row( $query, 'OBJECT' );
    $this->assert_not_empty( $commission );

    // If the commission exists, delete it!
    $wpdb->delete( $table, ['id' => $commission->id], ['id' => '%d'] );
    $this->db_notice( 'commission deleted: ' . $commission->id );
  }

}