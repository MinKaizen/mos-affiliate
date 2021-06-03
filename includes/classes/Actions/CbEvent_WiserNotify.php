<?php declare(strict_types=1);

namespace MOS\Affiliate\Actions;

use \MOS\Affiliate\AbstractAction;
use \MGC\Logger\Logger;
use \MOS\Affiliate\DataStructs\ClickbankEvent;
use function wp_remote_post;

class CbEvent_WiserNotify extends AbstractAction {

  const TRANSACTION_TYPES = ['SALE', 'TEST_SALE'];
  const WEBHOOK_URL = 'https://is.wisernotify.com/api/custom/log?ti=2h0helkgpqcn8w&fuid=603d76c7d1d139001333366d';

  protected $hook = 'clickbank_event';

  public function __construct() {
    $this->logger = new Logger( 'mos-affiliate', 'clickbank_wiser_notify.log' );
  }

  public function handler( $data ): void {
    if ( !( $data instanceof ClickbankEvent ) ) {
      return;
    }

    $args = [
      'headers' => [
        'Accept' => 'application/json',
        'Content-type' => 'application/json',
      ],
      'body' => $data->json(),
    ];
    wp_remote_post( self::WEBHOOK_URL, $args );
    $this->logger->log( $args['body'] );
  }

}