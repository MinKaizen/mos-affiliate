<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use \WP_CLI;

class SuccessTest extends Test {

  public function test_main(): void {
    WP_CLI::line('Never gonna give you up!');
  }

}