<?php declare(strict_types=1);

namespace MOS\Affiliate\Routes;

use \MOS\Affiliate\Adapters\ClickbankAdapter;
use \MGC\REST_Route\REST_Route;
use \WP_REST_Request;
use \WP_REST_Response;

class ClickbankEndpoint extends REST_Route {

  const SECRET_KEY = 'ANTOLAMAS61412';
  
  protected $root = 'mos-affiliate/v1';
  protected $route = 'clickbank-ins';
  protected $method = 'POST';

  public function handler( WP_REST_Request $request ): WP_REST_Response {
    $data = new ClickbankAdapter( $request->get_body() );
    return $this->response( $data );
  }

  public function permission(): bool {
    return true;
  }

}