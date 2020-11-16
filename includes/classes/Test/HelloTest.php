<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;

class HelloTest extends Test {

  public function main(): void {
    $this->assert_true(1==1);
    $this->assert_true(2==2);
    $this->assert_true(3==3);
    $this->assert_true(4==4);
    $this->assert_true(5==5);
    $this->assert_true(6==6);
    $this->assert_true(6==8);
  }

}