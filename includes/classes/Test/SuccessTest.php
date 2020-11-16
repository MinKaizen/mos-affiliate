<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use \WP_CLI;

class SuccessTest extends Test {

  public function main(): void {
    WP_CLI::line('Never gonna give you up!');
  }

}