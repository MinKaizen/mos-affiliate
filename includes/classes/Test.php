<?php

namespace MOS\Affiliate;

use \Exception;
use \WP_CLI;

class Test {

  protected function _before(): void {}

  protected function _after(): void {}

  public function run(): void {
    $class_name = get_class( $this );

    // Get class name stub
    foreach ( explode( '\\', $class_name ) as $part ) {
      $class_name_short = $part;
    }
    
    foreach( get_class_methods( $class_name ) as $method ) {
      if ( strpos( $method, 'test_' ) !== 0 ) {
        continue;
      }
      $this->_before();
      $this->{$method}();
      $this->_after();
      WP_CLI::line( WP_CLI::colorize( "%g✔%n $class_name_short::$method" ) );
    }
  }


  protected function assert_equal( $value1, $value2, ...$labels ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 == $value2;
    $keys[0] = empty( $labels[0] ) ? 'value1' : $labels[0];
    $keys[1] = empty( $labels[1] ) ? 'value2' : $labels[1];
    $data = [
      $keys[0] => $value1,
      $keys[1] => $value2,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_equal_strict( $value1, $value2, ...$labels ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 === $value2;
    $keys[0] = empty( $labels[0] ) ? 'value1' : $labels[0];
    $keys[1] = empty( $labels[1] ) ? 'value2' : $labels[1];
    $data = [
      $keys[0] => $value1,
      $keys[1] => $value2,
    ];
    $this->assert( $condition, $data, $assertion );
  }



  protected function assert_not_equal( $value1, $value2, ...$labels ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 != $value2;
    $keys[0] = empty( $labels[0] ) ? 'value1' : $labels[0];
    $keys[1] = empty( $labels[1] ) ? 'value2' : $labels[1];
    $data = [
      $keys[0] => $value1,
      $keys[1] => $value2,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_not_equal_strict( $value1, $value2, ...$labels ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 !== $value2;
    $keys[0] = empty( $labels[0] ) ? 'value1' : $labels[0];
    $keys[1] = empty( $labels[1] ) ? 'value2' : $labels[1];
    $data = [
      $keys[0] => $value1,
      $keys[1] => $value2,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_true( $expression, ...$labels ): void {
    $assertion = __FUNCTION__;
    $condition = $expression == true;
    $keys[0] = empty( $labels[0] ) ? 'expression' : $labels[0];
    $data = [
      $keys[0] => $expression,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_true_strict( $expression, ...$labels ): void {
    $assertion = __FUNCTION__;
    $condition = $expression === true;
    $keys[0] = empty( $labels[0] ) ? 'expression' : $labels[0];
    $data = [
      $keys[0] => $expression,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_false( $expression, ...$labels ): void {
    $assertion = __FUNCTION__;
    $condition = $expression == false;
    $keys[0] = empty( $labels[0] ) ? 'expression' : $labels[0];
    $data = [
      $keys[0] => $expression,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_false_strict( $expression, ...$labels ): void {
    $assertion = __FUNCTION__;
    $condition = $expression === false;
    $keys[0] = empty( $labels[0] ) ? 'expression' : $labels[0];
    $data = [
      $keys[0] => $expression,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_string_contains( string $haystack, string $needle, ...$labels ): void {
    $assertion = __FUNCTION__;
    $condition = ( strpos( $haystack, $needle ) !== false );
    $keys[0] = empty( $labels[0] ) ? 'haystack' : $labels[0];
    $keys[1] = empty( $labels[1] ) ? 'needle' : $labels[1];
    $data = [
      $keys[0] => $haystack,
      $keys[1] => $needle,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_has_key( array $array, $key, ...$labels ): void {
    $assertion = __FUNCTION__;
    $condition = array_key_exists( $key, $array );
    $keys[0] = empty( $labels[0] ) ? 'array' : $labels[0];
    $keys[1] = empty( $labels[1] ) ? 'key' : $labels[1];
    $data = [
      $keys[0] => $array,
      $keys[1] => $key,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_instanceof( $instance, string $class, ...$labels ): void {
    $assertion = __FUNCTION__;
    $condition = $instance instanceof $class;
    $keys[0] = empty( $labels[0] ) ? 'instance' : $labels[0];
    $keys[1] = empty( $labels[1] ) ? 'class' : $labels[1];
    $data = [
      $keys[0] => $instance,
      $keys[1] => $class,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert( $condition, $data, string $assertion ): void {
    if ( $condition ) {
      return;
    }

    $e = new Exception();
    $trace_string = $e->getTraceAsString();
    $trace_formatted = $this->format_trace( $trace_string );
    
    WP_CLI::line( $trace_formatted );

    $this->print_yaml( $data );
    $this->print_error( $assertion );
  }

  
  protected function print_error( string $message ): void {
    $colorized_message = WP_CLI::colorize( '%1%W' . $message . '%n%N');
    WP_CLI::error( $colorized_message );
  }
  
  
  protected function db_notice( string $message ): void {
    $colorized_message = WP_CLI::colorize( '%b✦%n ' . $message);
    WP_CLI::line( $colorized_message );
  }


  protected function print_yaml( $original ): void {
    if ( empty( $original ) ) {
      WP_CLI::warning( "No debug data passed" );
      return;
    }
    $debug_dump = [
      [
        'debug_dump' => $original,
      ],
    ];

    \WP_CLI\Utils\format_items( 'yaml', $debug_dump, 'debug_dump' );
  }


  protected function format_trace( string $original ): string {
    $exploded = explode("\n", print_r( $original, true));

    // Cut and colorize
    foreach ( $exploded as &$line ) {
      $line = preg_replace( '/(.*)(\/[a-zA-Z0-9]+\.php)(\(\d+\))(.*)/', '%5%W$2%6%W$3%n%N$4', $line );
      $line = WP_CLI::colorize( $line );
    }

    $formatted = 'Stack trace:' . PHP_EOL;

    foreach ( $exploded as $line ) {
      if ( strpos( $line, "[internal function]" ) === false ) {
        $formatted .= $line . PHP_EOL;
      } else {
        break;
      }
    }

    return $formatted;
  }


}