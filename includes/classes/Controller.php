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
  
  protected $variables = [];
  private $data = [];


  public function __construct() {
    foreach ( $this->variables as $variable ) {
      if ( method_exists( $this, $variable ) ) {
        $this->data[$variable] = $this->$variable();
      }
    }
  }


  public function data() {
    return $this->data;
  }
  
  
  /**
   * Get controller based on view name
   *
   * @param string $view_name           Name of the view file (snake case)
   * @return Controller    Controller with the matching CamelCase name, or FALSE
   */
  public static function get_controller( string $view_name ) {
    $class_name = self::controller_class_name( $view_name );
    
    // Check if controller exists
    if ( class_exists( $class_name ) ) {
      $controller = new $class_name();
    } else {
      $controller = false;
    }

    return $controller;
  }


  /**
   * Get class name of a controller (namespaced) given a view name
   *
   * @param string $view_name         e.g. "my_view"
   * @return string $class_name       e.g. "MOS\Affiliate\MyViewController"
   */
  private static function controller_class_name( string $view_name ): string {
    $camel_case = self::snake_to_camel_case( $view_name, true );
    $class_name = NS . 'Controller\\' . $camel_case . 'Controller';
    return $class_name;
  }


   /**
   * Convert snake case to camel case
   *
   * @param string $name            Name in snake case: abc_defg_hij
   * @param boolean $capitalize     Whether or not to capitalize the first word
   * @return $camel_case            Name in camel case: AbcDefgHij
   */
  private static function snake_to_camel_case( string $name, bool $capitalize=false ) {
    $camel_case = str_replace( ' ', '', ucwords( implode( " ", explode( "_", $name ) ) ) );
    $camel_case = $capitalize ? $camel_case : lcfirst($camel_case);
    return $camel_case;
  }
}