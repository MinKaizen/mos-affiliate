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

  protected function assert_true( $expression, string $message ): void {
    $condition = $expression == true;
    $this->assert( $condition, $message );
  }


  protected function assert_false( $expression, string $message ): void {
    $condition = $expression == false;
    $this->assert( $condition, $message );
  }


  protected function assert_has_key( $needle, array $haystack, $message ): void {
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
    $trace = explode("\n", print_r(str_replace(PLUGIN_DIR, '', $e->getTraceAsString()), true));

    foreach ( $trace as $line ) {
      if ( strpos( $line, "CommandFactory.php" ) === false ) {
        WP_CLI::line($line);
      } else {
        break;
      }
    }

    $this->print_error( $message );
  }

  
  protected function print_error( string $message ): void {
    $colorized_message = WP_CLI::colorize( '%5%W' . $message . '%n%N');
    WP_CLI::error( $colorized_message );
  }


}