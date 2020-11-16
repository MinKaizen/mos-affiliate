<?php

namespace MOS\Affiliate;

use \WP_CLI;

class CLI {

  private $tests = [
    'hello',
    'rick_roll',
  ];


  public function test( $args ): void {
    list( $test_name ) = $args;    

    if ( $test_name == 'all' ) {
      $this->test_all();
    } elseif ( empty( $test_name ) ) {
      WP_CLI::error( "Please specify a test" );
    } elseif ( in_array( $test_name, $this->tests ) ) {
      $this->test_single( $test_name );
    } else {
      WP_CLI::error( "$test_name is not a registered test" );
    }

    // $success_message = "✓✓✓ All tests passed. You are awesome!";
    $success_message = WP_CLI::colorize( "%2%w✓✓✓ All tests passed. You are awesome!" );
    WP_CLI::success( $success_message );
  }


  public function test_all() {
    foreach ( $this->tests as $test_stub ) {
      $this->test_single( $test_stub );
    }
  }


  private function test_single( string $test_stub ): void {
    $test_class_name = $this->stub_to_class_name( $test_stub );
      $test = new $test_class_name();
      $test->run();
  }


  private function stub_to_class_name( string $stub ): string {
    $pascalized = $this->snake_to_pascal( $stub );
    return NS . 'Test\\' . $pascalized . 'Test';
  }


  private function snake_to_pascal( string $snake ): string {
    $split_words = str_replace( '_', ' ', $snake );
    $capitalized = ucwords( $split_words );
    $pascal = str_replace( ' ', '', $capitalized );
    return $pascal;
  }


}