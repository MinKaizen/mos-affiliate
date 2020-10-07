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

class MosAffiliateController {
  
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
   * @return MosAffiliateController    Controller with the matching CamelCase name, or FALSE
   */
  public static function get_controller( string $view_name ) {
    global $mos_affiliate_plugin;

    $controller_name = self::snake_to_camel_case( $view_name, true ) . 'Controller';
    $controller_file_name = "{$mos_affiliate_plugin->path}includes/controllers/$controller_name.php";

    // Check if controller exists
    if ( file_exists( $controller_file_name ) ) {
      require_once( $controller_file_name );
      $controller = new $controller_name();
    } else {
      $controller = false;
    }

    return $controller;
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