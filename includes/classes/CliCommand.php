<?php declare(strict_types=1);

namespace MOS\Affiliate;

use \WP_CLI;

class CliCommand {


  public function run( array $pos_args, array $assoc_args ): void {
    // To be overridden
  }

  
  protected function _on_exit(): void {
    // To be overridden
  }

  
  protected final function get_input( string $prompt ): string {
    fwrite( STDOUT, $prompt . PHP_EOL );
    $answer = trim( (string) fgets( STDIN ) );
    return $answer;
  }

  
  protected final function get_confirmation( string $prompt, array $passed_opts=[], bool $abort=false ): bool {
    $defaults = [
      'confirm_word' => 'y',
      'color' => '',
    ];
    $opts = array_replace( $defaults, $passed_opts );
    $answer = '';
    $prompt = $this->colorize( $prompt, $opts['color'] ) . " [{$opts['confirm_word']}] to confirm...";
    
    while ( $answer == '' ) {
      $answer = $this->get_input( $prompt );
    }

    if ( $abort && $answer !== $opts['confirm_word'] ) {
      $this->exit( 'aborted' );
    }

    return $answer === $opts['confirm_word'];
  }

  
  protected final function get_any_key( string $prompt='' ): void {
    $prompt .= $prompt ? ' ' : '';
    $prompt .= '[any key] to continue';
    fwrite( STDOUT, $prompt . PHP_EOL );

    while ( !isset( $answer ) ) {
      $answer = trim( (string) fgets( STDIN ) );
    }
  }

  
  protected final function exit( string $message='', string $color=''  ): void {
    $divider = $this->colorize( '==============================', $color );
    $message = $divider . PHP_EOL . $message . PHP_EOL . $divider;
    $this->_on_exit();
    if ( !empty( $message ) ) {
      WP_CLI::line( $message );
    }
    exit;
  }

  
  protected final function colorize( string $message, string $color='' ): string {
    if ( empty( $color ) ) {
      return $message;
    }

    $color_map = [
      'danger' => '%1',
      'success' => '%g',
      'error' => '%r',
    ];

    if ( !in_array( $color, array_keys( $color_map ) ) ) {
      return $message;
    }

    return WP_CLI::colorize( $color_map[$color] . $message . '%n' );
  }

  
}