<?php

namespace MOS\Affiliate;

class Plugin {


  public function __construct() {
    require( PLUGIN_DIR . "/includes/config/mis.php" );
    require( PLUGIN_DIR . "/includes/config/mis_networks.php" );
    require( PLUGIN_DIR . "/includes/helpers/utils.php" );
  }


  public function init() {
    $this->load_admin();
    $this->load_scripts();
    $this->register_shortcodes();
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


  private function register_shortcodes() {
    $dir = new \DirectoryIterator( PLUGIN_DIR . '/includes/classes/Shortcode' );

    foreach ($dir as $fileinfo) {
      if ( $fileinfo->isDot() ) {
        continue;
      }
      $sc_class_name = Shortcode::shortcode_class_name( $fileinfo->getFilename() );
      $sc_class = new $sc_class_name;
      $sc_class->register();
    }
  }

}