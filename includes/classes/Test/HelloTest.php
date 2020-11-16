<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;

class HelloTest extends Test {

  public function main(): void {
    $this->assert_true(1==1, '1 equals 1');
    $this->assert_true(2==2, '2 equals 2');
    $this->assert_true(3==3, '3 equals 3');
    $this->assert_true(4==4, '4 equals 4');
    $this->assert_true(5==5, '5 equals 5');
    $this->assert_true(6==6, '6 equals 6');
    $this->assert_true(6==8, '6 equals 8');
  }

}