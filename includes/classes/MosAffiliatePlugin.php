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

}