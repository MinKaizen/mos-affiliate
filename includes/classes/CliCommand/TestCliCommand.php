<?php declare(strict_types=1);

namespace MOS\Affiliate\CliCommand;

use MOS\Affiliate\CliCommand;
use \WP_CLI;

use function MOS\Affiliate\class_name;

class TestCliCommand extends CliCommand {

  protected $command = 'test';

  private $tests = [
    'database_class',
    'pre_conditions',
    'migrations',
    'access_redirects',
    'user_class',
    'commission_class',
    'user_shortcodes',
    'sponsor_shortcodes',
    'levels_exist',
  ];


  public function run( array $pos_args, array $assoc_args ): void {
    list( $test_name ) = $pos_args;    

    if ( $test_name == 'all' ) {
      $this->test_all();
    } elseif ( empty( $test_name ) ) {
      WP_CLI::error( "Please specify a test" );
    } elseif ( in_array( $test_name, $this->tests ) ) {
      $this->test_single( $test_name );
    } else {
      WP_CLI::error( "$test_name is not a registered test" );
    }

    $success_message = WP_CLI::colorize( "%2%w✔✔✔ All tests passed. You are awesome!%n%N" );
    WP_CLI::success( $success_message );
  }


  public function test_all() {
    foreach ( $this->tests as $test_stub ) {
      $this->test_single( $test_stub );
    }
  }


  private function test_single( string $test_stub ): void {
    $test_class_name = class_name( $test_stub . '_test', 'Test' );
    $test = new $test_class_name();
    $test->run();
  }


}