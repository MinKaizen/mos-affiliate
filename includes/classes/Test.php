<?php

namespace MOS\Affiliate;

use \Exception;
use \WP_CLI;

abstract class Test {

  protected function _before(): void {}

  abstract function main(): void;

  protected function _after(): void {}

  public function run(): void {
    try {
      $this->_before();
      $this->main();
      $this->_after();
    } catch ( Exception $e ) {
      $this->print_error( $e );
    }
  }


  protected function assert_equal( $value1, $value2, ...$labels ): void {
    $assertion = "assert_equal";
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
    $assertion = "assert_equal_strict";
    $condition = $value1 === $value2;
    $keys[0] = empty( $labels[0] ) ? 'value1' : $labels[0];
    $keys[1] = empty( $labels[1] ) ? 'value2' : $labels[1];
    $data = [
      $keys[0] => $value1,
      $keys[1] => $value2,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_true( $expression, ...$labels ): void {
    $assertion = "assert_true";
    $condition = $expression == true;
    $keys[0] = empty( $labels[0] ) ? 'expression' : $labels[0];
    $data = [
      $keys[0] => $expression,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_false( $expression, ...$labels ): void {
    $assertion = "assert_false";
    $condition = $expression == false;
    $keys[0] = empty( $labels[0] ) ? 'expression' : $labels[0];
    $data = [
      $keys[0] => $expression,
    ];
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_string_contains( string $haystack, string $needle, ...$labels ): void {
    $assertion = "assert_string_contains";
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
    $assertion = "assert_has_key";
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
    $assertion = "assert_instanceof";
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
    $trace_colorized = $this->colorize_trace( $trace_formatted );
    
    WP_CLI::line( $trace_colorized );

    $this->print_yaml( $data );
    $this->print_error( $assertion );
  }

  
  protected function print_error( string $message ): void {
    $colorized_message = WP_CLI::colorize( '%1%W' . $message . '%n%N');
    WP_CLI::error( $colorized_message );
  }


  protected function print_yaml( $original ): void {
    $debug_dump = [
      [
        'debug_dump' => $original,
      ],
    ];

    \WP_CLI\Utils\format_items( 'yaml', $debug_dump, 'debug_dump' );
  }


  protected function format_trace( string $original ): string {
    $exploded = explode("\n", print_r(str_replace(PLUGIN_DIR, '', $original), true));
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


  protected function colorize_trace( string $original ): string {
    $exploded = explode( PHP_EOL, $original );

    foreach ( $exploded as &$line ) {
      $line = preg_replace( '/(.*)(\/[a-zA-Z0-9]+\.php)(\(\d+\))(.*)/', '$1%5%W$2%6%W$3%n%N$4', $line );
    }

    $colorized = WP_CLI::colorize( implode( PHP_EOL, $exploded ) );

    return $colorized;
  }

}