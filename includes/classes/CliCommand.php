<?php declare(strict_types=1);

namespace MOS\Affiliate;

use \WP_CLI;

abstract class CliCommand {
  
  const GLOBAL_COMMAND = 'mosa';

  protected $command = '';


  abstract function run( array $pos_args, array $assoc_args ): void;


  public function register(): void {
    if ( empty( $this->command ) ) {
      return;
    }
    $command = implode( ' ', [self::GLOBAL_COMMAND, $this->command] );
    WP_CLI::add_command( $command, [$this, 'run' ] );
  }

  
}