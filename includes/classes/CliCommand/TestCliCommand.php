<?php declare(strict_types=1);

namespace MOS\Affiliate\CliCommand;

use MOS\Affiliate\CliCommand;
use \WP_CLI;

use function MOS\Affiliate\class_name;

class TestCliCommand extends CliCommand {

  protected $command = 'test';

  private $pre_tests = [
    'db_tables',
    'levels_exist',
    'db_functions',
  ];

  private $tests = [
    'access_redirects',
    'commission_class',
    'commission_table',
    'sponsor_shortcodes',
    'user_class',
    'user_shortcodes',
  ];


  public function run( array $pos_args, array $assoc_args ): void {
    list( $test_name ) = $pos_args;    

    if ( empty( $test_name ) ) {
      WP_CLI::error( "Please specify a test" );
    } elseif ( $test_name == 'pre' ) {
      $this->test_multiple( $this->pre_tests );
    } elseif ( $test_name == 'all' ) {
      $this->test_multiple( $this->pre_tests );
      $this->test_multiple( $this->tests );
    } elseif ( in_array( $test_name, $this->tests ) ) {
      $this->test_multiple( $this->pre_tests );
      $this->test_single( $test_name );
    } else {
      WP_CLI::error( "$test_name is not a registered test" );
    }

    $success_message = WP_CLI::colorize( "%2%w✔✔✔ All tests passed. You are awesome!%n%N" );
    WP_CLI::success( $success_message );
  }


  private function test_multiple( array $tests ): void {
    $num_tests = count( $tests );
    $progress = \WP_CLI\Utils\make_progress_bar( 'Progress', $num_tests );
    foreach ( $tests as $test_stub ) {
      $this->test_single( $test_stub );
      $progress->tick();
    }
  }


  private function test_single( string $test_stub ): void {
    $test_class_name = class_name( $test_stub . '_test', 'Test' );
    $test = new $test_class_name();
    $test->run();
  }


}