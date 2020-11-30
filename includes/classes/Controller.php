<?php
/**
 * Base class for controllers
 * 
 * Controller Names use CapitalCamelCase
 * Controller Names should match the snake_case_name of the view and append "Controller"
 * 
 * View name: this_is_an_example
 * Controller name: ThisIsAnExampleController
 */

namespace MOS\Affiliate;

class Controller {

  const EXPORT_PREFIX = 'export_';


  public function get_vars(): array {
    $methods = get_class_methods( $this );
    $export_vars = [];

    foreach ( $methods as $method ) {
      if ( strpos( $method, self::EXPORT_PREFIX ) !== 0 ) {
        continue;
      }
      $var_name = str_replace( self::EXPORT_PREFIX, '', $method );
      $export_vars[$var_name] = $this->$method();
    }

    return $export_vars;
  }


  public static function get_controller( string $view_name ): self {
    $class_name = class_name( $view_name, 'Controller' );
    
    if ( class_exists( $class_name ) ) {
      $controller = new $class_name();
    } else {
      $controller = new self(); // Empty controller
    }

    return $controller;
  }


}