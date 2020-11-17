<?php

namespace MOS\Affiliate;

abstract class AbstractRoute {

  protected $namespace='mos-affiliate';
  protected $version=1;
  protected $route='';
  protected $method='GET';

  abstract function serve(): void;

  public function register() {
    \add_action( 'rest_api_init', function() {
      $namespace = $this->namespace . '/v' . $this->version;
      $route = $this->route;
      $args = [
        'methods' => $this->method,
        'callback' => [$this, 'serve']
      ];
      \register_rest_route( $namespace, $route, $args );
    } );
  }

}