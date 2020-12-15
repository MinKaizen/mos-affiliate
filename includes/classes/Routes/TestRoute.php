<?php declare(strict_types=1);

namespace MOS\Affiliate\Routes;

use \MGC\REST_Router\AbstractRoute;
use \WP_REST_Request;
use \WP_REST_Response;

class TestRoute extends AbstractRoute {

  protected $root = 'mos-affiliate/v1';
  protected $route = 'test';
  protected $method = 'GET';

  public function handler( WP_REST_Request $request ): WP_REST_Response {
    return $this->response("It works!");
  }

}