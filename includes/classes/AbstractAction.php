<?php declare(strict_types=1);

namespace MOS\Affiliate;

use Error;
use function \add_action;

abstract class AbstractAction {

  const HANDLER_FUNCTION_NAME = 'handler';

  protected $hook;
  protected $priority = 10;
  protected $args = 1;

  public function register(): void {
    if ( !isset( $this->hook ) ) {
      throw new Error('Action must define hook property');
    }

    if ( !is_string( $this->hook ) ) {
      throw new Error( 'Action->hook must be of type string' );
    }

    if ( !method_exists( $this, self::HANDLER_FUNCTION_NAME ) ) {
      throw new Error( 'Action must implement a public function named ' . self::HANDLER_FUNCTION_NAME );
    }

    add_action( $this->hook, [$this, self::HANDLER_FUNCTION_NAME], $this->priority, $this->args );
  }

}