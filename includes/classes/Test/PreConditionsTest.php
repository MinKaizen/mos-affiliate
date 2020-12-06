<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

class PreConditionsTest extends Test {


  public function test_uap(): void {
    $this->assert_db_table_exists( 'uap_affiliates' );
    $this->assert_db_table_exists( 'uap_affiliate_referral_users_relations' );
  }


}