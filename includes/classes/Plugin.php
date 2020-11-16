<?php

namespace MOS\Affiliate;

class Plugin {


  public function __construct() {
    require( PLUGIN_DIR . "/includes/config/caps.php" );
    require( PLUGIN_DIR . "/includes/config/levels.php" );
    require( PLUGIN_DIR . "/includes/config/mis.php" );
    require( PLUGIN_DIR . "/includes/helpers/utils.php" );
  }


  public function init() {
    $this->load_admin();
    $this->load_scripts();
    Shortcode::register_all();
    AccessRedirect::register_all();
  }


  private function load_admin() {
    $admin = new Admin();
    $admin->init();
  }


  private function load_scripts() {
    \add_action( 'wp_enqueue_scripts', function() {
      \wp_enqueue_script( 'mosAffiliate', PLUGIN_URL . 'dist/js/mosAffiliate.js', ['jquery'], '1.0.0', true );
    });
  }


}