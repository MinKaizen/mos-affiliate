<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

use function \MOS\Affiliate\class_name;
use function \MOS\Affiliate\snake_to_pascal_case;
use function \MOS\Affiliate\pascal_to_snake_case;
use function \MOS\Affiliate\first_non_empty_element;

class UtilsTest extends Test {

  public function test_class_name(): void {
    $this->assert_equal( class_name( 'hello' ), 'MOS\\Affiliate\\Hello' );
    $this->assert_equal( class_name( 'hello', 'Folder' ), 'MOS\\Affiliate\\Folder\\Hello' );
    $this->assert_equal( class_name( 'hello_world', 'Folder' ), 'MOS\\Affiliate\\Folder\\HelloWorld' );
    $this->assert_equal( class_name( 'hello_world', 'Folder/subfold' ), 'MOS\\Affiliate\\Folder\\subfold\\HelloWorld' );
  }


  public function test_snake_to_pascal_case(): void {
    $this->assert_equal( snake_to_pascal_case( 'hello' ), 'Hello' );
    $this->assert_equal( snake_to_pascal_case( 'hello_world' ), 'HelloWorld' );
    $this->assert_equal( snake_to_pascal_case( '_hello_world_' ), 'HelloWorld' );
  }


  public function test_pascal_to_snake_case(): void {
    $this->assert_equal( pascal_to_snake_case( '' ), '' );
    $this->assert_equal( pascal_to_snake_case( 'Hello' ), 'hello' );
    $this->assert_equal( pascal_to_snake_case( 'HelloWorld' ), 'hello_world' );
    $this->assert_equal( pascal_to_snake_case( 'HelloWorldWar' ), 'hello_world_war' );
  }


  public function test_first_non_empty_element(): void {
    $array = [
      0,
      null,
      '',
      'definitely_valid',
    ];

    $this->assert_equal( first_non_empty_element( $array ), 'definitely_valid' );
  }

}