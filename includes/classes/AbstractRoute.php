<?php

namespace MOS\Affiliate;

use function MOS\Affiliate\snake_to_pascal_case;

abstract class AbstractRoute {

  protected $namespace='mos-affiliate';
  protected $base = '';
  protected $version=1;
  protected $route='';
  protected $method='GET';

  abstract function serve(): void;

  public function register() {
    $namespace = $this->namespace . '/v' . $this->version . '/' . $this->base;
    $route = $this->route;
    $args = [
      'methods' => $this->method,
      'callback' => [$this, 'serve']
    ];
    \register_rest_route( $namespace, $route, $args );
  }


  public static function class_name( string $stub ): string {
    $pascalized = snake_to_pascal_case( $stub );
    return NS . 'Route\\' . $pascalized;
  }


}