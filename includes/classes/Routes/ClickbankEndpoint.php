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
    $data = new ClickbankEventAdapter( $request->get_body() );
    $logger = new Logger( 'mos-affiliate', 'clickbank_ins.log' );
    $logger->log( json_encode( $data->get_original() ), [$data->transaction_type, 'RECEIVE'] );
    $logger->log( json_encode( $data ), [$data->transaction_type, 'SEND'] );
    $logger->log( json_encode( $data ), ['SEND'] );
    return $this->response( $data );
  }

  public function permission(): bool {
    return true;
  }

}