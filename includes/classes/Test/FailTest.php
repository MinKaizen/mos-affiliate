<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;

class FailTest extends Test {

  public function main(): void {
    $this->assert_true( true==false );
  }

}