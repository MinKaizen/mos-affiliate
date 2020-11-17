<?php

namespace MOS\Affiliate;

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
    $pascalized = self::snake_to_pascal( $stub );
    return NS . 'Route\\' . $pascalized;
  }


  private static function snake_to_pascal( string $snake ): string {
    $split_words = str_replace( '_', ' ', $snake );
    $capitalized = ucwords( $split_words );
    $pascal = str_replace( ' ', '', $capitalized );
    return $pascal;
  }

}