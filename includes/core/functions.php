<?php

namespace MOS\Affiliate;

/**
 * Get view html as string
 *
 * @param string  $view_name   Name of the view
 * @return string $view        The view's html as a string
 */
function get_view( string $view_name ) {
  $view_file_name = PLUGIN_DIR . "/includes/views/$view_name.php";
  
  // Check if view exits
  if ( !file_exists( $view_file_name ) ) {
    return '';
  }
  
  // Load controller
  $controller = Controller::get_controller( $view_name );

  // If controller exists, extract its data
  if ( $controller !== false ) {
    extract( $controller->data() );
  }

  // Get view html as a string
  ob_start();
  include( $view_file_name );
  $view = ob_get_clean();
  
  return $view;
}


/**
 * Convert snake_case to PascalCase
 *
 * @param string $name            Name in snake case: abc_defg_hij
 * @return $camel_case            Name in camel case: AbcDefgHij
 */
function snake_to_pascal_case( string $name ): string {
  $pascal = str_replace( ' ', '', ucwords( implode( " ", explode( "_", $name ) ) ) );
  return $pascal;
}

/**
 * Convert PascalCase to snake_case
 *
 * @param string $snake_case
 * @return void
 */
function pascal_to_snake_case( string $snake_case ): string {
  preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $snake_case, $matches);
  $ret = $matches[0];
  foreach ($ret as &$match) {
    $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
  }
  return implode('_', $ret);
}