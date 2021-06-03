<?php declare(strict_types=1);

namespace MOS\Affiliate\Actions;

use \MOS\Affiliate\AbstractAction;
use \MGC\Logger\Logger;

class ClickbankEventRaw_Log extends AbstractAction {

  protected $hook = 'clickbank_event_raw';
  private $logger;

  public function __construct() {
    $this->logger = new Logger( 'mos-affiliate', 'clickbank_event.log' );
  }

  public function handler( $event ): void {
    $this->logger->log( json_encode( $event ), [$event->transactionType, 'RAW'] );
  }

}