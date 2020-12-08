<?php declare(strict_types=1);

namespace MOS\Affiliate;

use \WP_CLI;

abstract class CliCommand {
  
  const GLOBAL_COMMAND = 'mosa';

  protected $command = '';


  abstract function run( array $pos_args, array $assoc_args ): void;


  public function register(): void {
    if ( empty( $this->command ) ) {
      $error_msg = "Command name for " . __CLASS__ . " is empty!";
      WP_CLI::error( $error_msg );
    }
    $command = implode( ' ', [self::GLOBAL_COMMAND, $this->command] );
    WP_CLI::add_command( $command, [$this, 'run' ] );
  }

  
  protected function _on_exit(): void {
    // To be overridden by child class
  }

  
  protected final function get_input( string $prompt ): string {
    fwrite( STDOUT, $prompt . PHP_EOL );
    $answer = trim( (string) fgets( STDIN ) );
    return $answer;
  }

  
  protected final function get_confirmation( string $prompt, string $confirm_word='y' ): void {
    $answer = '';
    
    while ( $answer == '' ) {
      $answer = $this->get_input( $prompt );
    }

    if ( $answer !== $confirm_word ) {
      WP_CLI::line( 'aborted' );
      exit;
    }
  }

  
  protected final function exit(): void {
    $this->_on_exit();
    exit;
  }

  
}