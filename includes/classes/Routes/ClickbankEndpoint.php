<?php declare(strict_types=1);

namespace MOS\Affiliate\Routes;

use \MOS\Affiliate\Adapters\ClickbankEventAdapter;
use \MGC\REST_Route\REST_Route;
use \MGC\Logger\Logger;
use \WP_REST_Request;
use \WP_REST_Response;

class ClickbankEndpoint extends REST_Route {

  const SECRET_KEY = 'ANTOLAMAS61412';
  
  protected $root = 'mos-affiliate/v1';
  protected $route = 'clickbank-ins';
  protected $method = 'POST';

  public function handler( WP_REST_Request $request ): WP_REST_Response {
    $clickbank_event = new ClickbankEventAdapter( $request->get_body() );
    $logger = new Logger( 'mos-affiliate', 'clickbank_ins.log' );
    $logger->log( json_encode( $clickbank_event->get_original() ), [$clickbank_event->transaction_type, 'RECEIVE'] );
    $logger->log( json_encode( $clickbank_event ), [$clickbank_event->transaction_type, 'SEND'] );
    \do_action( 'clickbank_event', $clickbank_event );
    return $this->response( $clickbank_event );
  }

}