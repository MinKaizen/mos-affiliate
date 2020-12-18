<?php declare(strict_types=1);

namespace MOS\Affiliate;

use Error;
use function MGC\Async\add_action_async;
use function \add_action;

abstract class ActionHook {

  const HANDLER_FUNCTION_NAME = 'handler';

  protected $hook;
  protected $priority = 10;
  protected $args = 1;
  protected $async = false;

  public function register(): void {
    if ( !isset( $this->hook ) ) {
      throw new Error('ActionHook must define hook property');
    }

    if ( !is_string( $this->hook ) ) {
      throw new Error( 'ActionHook->hook must be of type string' );
    }

    if ( !method_exists( $this, self::HANDLER_FUNCTION_NAME ) ) {
      throw new Error( 'ActionHook must implement a public function named ' . self::HANDLER_FUNCTION_NAME );
    }

    if ( $this->async ) {
      add_action_async( $this->hook, [$this, 'handler'], $this->priority, $this->args );
    } else {
      add_action( $this->hook, [$this, self::HANDLER_FUNCTION_NAME], $this->priority, $this->args );
    }
  }

}