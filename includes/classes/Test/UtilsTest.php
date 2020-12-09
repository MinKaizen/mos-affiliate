<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

use function \MOS\Affiliate\class_name;
use function \MOS\Affiliate\snake_to_pascal_case;
use function \MOS\Affiliate\pascal_to_snake_case;
use function \MOS\Affiliate\first_non_empty_element;
use function \MOS\Affiliate\is_dateable;
use function \MOS\Affiliate\proper_to_kebab_case;
use function \MOS\Affiliate\snake_to_proper_case;

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


  public function test_is_dateable(): void {
    // Valid dates
    $this->assert_true( is_dateable('2020-01-01') ); // 2020 New Years
    $this->assert_true( is_dateable('2020-1-1') ); // without leading 0's
    $this->assert_true( is_dateable('2020-01-31') ); // Last day of Jan
    $this->assert_true( is_dateable('2020-12-31') ); // Last day of Dec
    $this->assert_true( is_dateable('2020-02-29') ); // Leap year
    $this->assert_true( is_dateable('9999-01-01') ); // really far away year
    
    // Invalid dates
    $this->assert_false( is_dateable('002020-01-01') ); // year has leading zero
    $this->assert_false( is_dateable('2020-01-001') ); // 3-digit day
    $this->assert_false( is_dateable('2020-001-01') ); // 3-digit month
    $this->assert_false( is_dateable('2020-00-01') ); // month 0
    $this->assert_false( is_dateable('2020-13-01') ); // month 13
    $this->assert_false( is_dateable('2020-01-34') ); // day 34
    $this->assert_false( is_dateable('2020-01-00') ); // day 0
    
    // Wrong format
    $this->assert_false( is_dateable('01-01-2020') ); // dd-mm-yyyy
    $this->assert_false( is_dateable('01/01/2020') ); // dd/mm/yyyy
    $this->assert_false( is_dateable('2020/01/01') ); // yyyy/mm/dd

    
    // Non dates
    $this->assert_false( is_dateable('some-string') );
    $this->assert_false( is_dateable('xxxx-xx-xx') );
  }


  public function test_proper_to_kebab_case(): void {
    $this->assert_equal( proper_to_kebab_case( '' ), '' );
    $this->assert_equal( proper_to_kebab_case( 'Martin Cao' ), 'martin-cao' );
    $this->assert_equal( proper_to_kebab_case( 'Martin Cao WAS HERE' ), 'martin-cao-was-here' );
  }


  public function test_snake_to_proper_case(): void {
    $this->assert_equal( snake_to_proper_case( '' ), '' );
    $this->assert_equal( snake_to_proper_case( 'hello' ), 'Hello' );
    $this->assert_equal( snake_to_proper_case( 'hello_world' ), 'Hello World' );
    $this->assert_equal( snake_to_proper_case( 'hello_world_this' ), 'Hello World This' );
  }

}