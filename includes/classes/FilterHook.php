<?php declare(strict_types=1);

namespace MOS\Affiliate;

use Error;
use function \add_filter;

abstract class FilterHook {

  const HANDLER_FUNCTION_NAME = 'handler';

  protected $hook;
  protected $priority = 10;
  protected $args = 1;

  public function register(): void {
    if ( !isset( $this->hook ) ) {
      throw new Error('FilterHook must define hook property');
    }

    if ( !is_string( $this->hook ) ) {
      throw new Error( 'FilterHook->hook must be of type string' );
    }

    if ( !method_exists( $this, self::HANDLER_FUNCTION_NAME ) ) {
      throw new Error( 'FilterHook must implement a public function named ' . self::HANDLER_FUNCTION_NAME );
    }

    add_filter( $this->hook, [$this, self::HANDLER_FUNCTION_NAME], $this->priority, $this->args );
  }

}