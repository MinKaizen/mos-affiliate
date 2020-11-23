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


  protected function assert_is_int( $var, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = is_int( $var );
    $data['var'] = $var;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_equal( $value1, $value2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 == $value2;
    $data['value1'] = $value1;
    $data['value2'] = $value2;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_equal_strict( $value1, $value2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 === $value2;
    $data['value1'] = $value1;
    $data['value2'] = $value2;
    $this->assert( $condition, $data, $assertion );
  }



  protected function assert_not_equal( $value1, $value2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 != $value2;
    $data['value1'] = $value1;
    $data['value2'] = $value2;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_not_equal_strict( $value1, $value2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 !== $value2;
    $data['value1'] = $value1;
    $data['value2'] = $value2;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_true( $expression, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $expression == true;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_true_strict( $expression, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $expression === true;
    $data['expression'] = $expression;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_false( $expression, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $expression == false;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_false_strict( $expression, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $expression === false;
    $data['expression'] = $expression;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_string_contains( string $haystack, string $needle, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = ( strpos( $haystack, $needle ) !== false );
    $data['haystack'] = $haystack;
    $data['needle'] = $needle;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_has_key( array $array, $key, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = array_key_exists( $key, $array );
    $data['array'] = $array;
    $data['key'] = $key;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_instanceof( $instance, string $class, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $instance instanceof $class;
    $data['instance'] = $instance;
    $data['class'] = $class;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_class_exists( string $class_name ): void {
    $assertion = __FUNCTION__;
    $condition = class_exists( $class_name );
    $data['class_name'] = $class_name;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert( $condition, $data=[], string $assertion ): void {
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

    WP_CLI\Utils\format_items( 'yaml', $debug_dump, 'debug_dump' );
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