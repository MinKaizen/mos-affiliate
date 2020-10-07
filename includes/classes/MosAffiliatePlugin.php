<?php

class MosAffiliatePlugin {

  private $path = '';
  private $url = '';


  public function __construct( string $plugin_file ) {
    $this->path = plugin_dir_path( $plugin_file );
    $this->url = plugin_dir_url( $plugin_file );
  }


  public function init() {
    $this->load_dependencies();
    $this->register_shortcodes();
  }


  private function load_dependencies() {

  }


  private function register_shortcodes() {
    // Campaign form
    add_shortcode( 'mos_uap_campaign_form', function(){
      return $this->get_view('campaign_form');
    });
  }


  /**
   * Get view html as string
   *
   * @param string  $view_name   Name of the view
   * @return string $view        The view's html as a string
   */
  private function get_view( string $view_name ) {
    $view_file_name = "{$this->path}/includes/views/$view_name.php";
    
    // Check if view exits
    if ( !file_exists( $view_file_name ) ) {
      return '';
    }
    
    // Get view html as a string
    ob_start();
    include( $view_file_name );
    $view = ob_get_clean();
    
    return $view;
  }

  /**
   * Convert snake case to camel case
   *
   * @param string $name            Name in snake case: abc_defg_hij
   * @param boolean $capitalize     Whether or not to capitalize the first word
   * @return $camel_case            Name in camel case: AbcDefgHij
   */
  private function snake_to_camel_case( string $name, bool $capitalize=false ) {
    $camel_case = str_replace( ' ', '', ucwords( implode( " ", explode( "_", $name ) ) ) );
    $camel_case = $capitalize ? $camel_case : lcfirst($camel_case);
    return $camel_case;
  }

}