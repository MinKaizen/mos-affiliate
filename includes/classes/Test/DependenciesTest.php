<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

class DependenciesTest extends Test {

  const FUNCTIONS = [
    'get_field',
  ];

  public function test_functions(): void {
    foreach ( self::FUNCTIONS as $function ) {
      $this->assert_true( function_exists( $function ), "Function $function is required." );
    }
  }

}