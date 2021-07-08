<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \stdClass;
use MOS\Affiliate\Test;

class CommissionsTableTest extends Test {

  public function test_table_exists(): void {
    $this->assert_db_table_exists( 'mos_commissions' );
  }

  public function test_table_schema(): void {
    $data = (Object) [
      'id' => 14,
      'date' => '2020-11-05',
      'amount' => 3.5,
      'description' => 'Monthly Partner (Trial)',
      'transaction_id' => 'asdf1',
      'campaign' => 'facebook',
      'actor_id' => 409,
      'earner_id' => 1,
      'payout_date' => '2020-04-01',
      'payout_method' => 'Bitoin',
      'payout_address' => 'sd54f5s4df6sd5f16sd54fs5d4f',
      'payout_transaction_id' => '60513-65856'
    ];
    $this->assert_db_row_exists( 'mos_commissions', $data );
  }

}