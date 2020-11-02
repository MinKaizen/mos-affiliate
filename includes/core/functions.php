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