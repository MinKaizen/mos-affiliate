<?php declare(strict_types=1);

namespace MOS\Affiliate\CliCommand;

use MOS\Affiliate\CliCommand;
use MOS\Affiliate\Plugin;
use \WP_CLI;

use function MOS\Affiliate\class_name;

class TestCliCommand extends CliCommand {

  protected $command = 'test';

  public function run( array $pos_args, array $assoc_args ): void {
    list( $test_name ) = $pos_args;
    $test_class = class_name( $test_name, 'Test' ) . 'Test';

    if ( empty( $test_name ) ) {
      WP_CLI::error( "Please specify a test" );
    } elseif ( $test_name == 'all' ) {
      $this->run_all();
    }elseif ( class_exists( $test_class ) ) {
      $this->run_single( $test_class );
    } else {
      WP_CLI::error( "$test_name ($test_class) is not a registered test" );
    }

    $this->success_message();
  }

  private function run_single( string $test_class ): void {
    $test = new $test_class();
    $test->run();
  }

  private function run_all(): void {
    $test_dir = Plugin::PLUGIN_DIR . 'includes/classes/Test';
    $dir = new \DirectoryIterator( $test_dir );
    foreach ( $dir as $fileinfo ) {
      if ( !$fileinfo->isDot() && !$fileinfo->isDir() ) {
        $class_name = PLUGIN::NS . 'Test' . '\\' . str_replace( '.php', '', $fileinfo->getFilename() );
        $test = new $class_name();
        $test->run();
      }
    }
  }

  private function success_message(): void {
    $success_message = WP_CLI::colorize( "%2%w✔✔✔ All tests passed. You are awesome!%n%N" );
    WP_CLI::success( $success_message );
  }

}