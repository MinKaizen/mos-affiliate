<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

use function \MOS\Affiliate\class_name;

class UtilsTest extends Test {

  public function test_class_name(): void {
    $this->assert_equal( class_name( 'hello' ), 'MOS\\Affiliate\\Hello' );
    $this->assert_equal( class_name( 'hello', 'Folder' ), 'MOS\\Affiliate\\Folder\\Hello' );
    $this->assert_equal( class_name( 'hello_world', 'Folder' ), 'MOS\\Affiliate\\Folder\\HelloWorld' );
    $this->assert_equal( class_name( 'hello_world', 'Folder/subfold' ), 'MOS\\Affiliate\\Folder\\subfold\\HelloWorld' );
  }

}