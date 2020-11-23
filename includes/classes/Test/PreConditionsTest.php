<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

class PreConditionsTest extends Test {


  public function test_uap(): void {
    $this->assert_class_exists( 'UAP_Main' );

  }


}