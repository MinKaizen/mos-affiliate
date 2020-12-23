<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

class DbTablesTest extends Test {

  public function test_tables_exist(): void {
    $this->assert_db_table_exists( 'mos_commissions' );
    $this->assert_db_table_exists( 'uap_affiliates' );
    $this->assert_db_table_exists( 'uap_visits' );
    $this->assert_db_table_exists( 'uap_referrals' );
  }

}