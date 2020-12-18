<?php declare(strict_types=1);

namespace MOS\Affiliate\ActionHooks;

use \MOS\Affiliate\ActionHook;
use \MGC\Logger\Logger;

class LogClickbankEvent extends ActionHook {

  protected $hook = 'clickbank_event';
  private $logger;

  public function __construct() {
    $this->logger = new Logger( 'mos-affiliate', 'clickbank_event.log' );
  }

  public function handler( $event ): void {
    $this->logger->log( json_encode( $event ), [$event->transaction_type, 'ADAPTED'] );
  }

}