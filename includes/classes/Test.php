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


  protected function assert_equal( $value1, $value2, string $message ): void {
    $condition = $value1 == $value2;
    $this->assert( $condition, $message );
  }


  protected function assert_equal_strict( $value1, $value2, string $message ): void {
    $condition = $value1 === $value2;
    $this->assert( $condition, $message );
  }


  protected function assert_true( $expression, string $message ): void {
    $condition = $expression == true;
    $this->assert( $condition, $message );
  }


  protected function assert_string_contains( string $haystack, string $needle, string $message ): void {
    $condition = ( strpos( $haystack, $needle ) !== false );
    $this->assert( $condition, $message );
  }


  protected function assert_false( $expression, string $message ): void {
    $condition = $expression == false;
    $this->assert( $condition, $message );
  }


  protected function assert_has_key( $needle, array $haystack, string $message ): void {
    $condition = array_key_exists( $needle, $haystack );
    $this->assert( $condition, $message );
  }


  protected function assert_instanceof( $instance, string $class, string $message ): void {
    $condition = $instance instanceof $class;
    $this->assert( $condition, $message );
  }


  protected function assert( $condition, string $message ): void {
    if ( $condition ) {
      return;
    }

    $e = new Exception();
    $trace_string = $e->getTraceAsString();
    $trace_formatted = $this->format_trace( $trace_string );
    $trace_colorized = $this->colorize_trace( $trace_formatted );
    
    WP_CLI::line( $trace_colorized );

    $this->print_error( $message );
  }

  
  protected function print_error( string $message ): void {
    $colorized_message = WP_CLI::colorize( '%1%W' . $message . '%n%N');
    WP_CLI::error( $colorized_message );
  }


  protected function print_yaml( $original ): void {
    $adjusted = [
      [
        'adjusted' => $original,
      ],
    ];

    \WP_CLI\Utils\format_items( 'yaml', $adjusted, 'adjusted' );
  }


  protected function format_trace( string $original ): string {
    $exploded = explode("\n", print_r(str_replace(PLUGIN_DIR, '', $original), true));
    $formatted = 'Stack trace:' . PHP_EOL;

    foreach ( $exploded as $line ) {
      if ( strpos( $line, "CommandFactory.php" ) === false ) {
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