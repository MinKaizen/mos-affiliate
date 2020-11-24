<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

class MigrationsTest extends Test {

  public function test_tables_exist(): void {
    $this->assert_db_table_exists( 'mos_commissions' );
  }

}