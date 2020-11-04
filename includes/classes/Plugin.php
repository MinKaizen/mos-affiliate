<?php

namespace MOS\Affiliate;

class Plugin {

  private $views = [
    // shortcode name => view name
    'mos_campaign_form' => 'campaign_form',
    'mos_campaign_list' => 'campaign_list',
    'mos_campaign_report' => 'campaign_report',
  ];

  public function __construct() {
    require( PLUGIN_DIR . "/includes/core/config.php" );
    require( PLUGIN_DIR . "/includes/core/functions.php" );
  }


  public function init() {
    $this->load_admin();
    $this->load_scripts();
    $this->register_views();
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


  private function register_views() {
    foreach ( $this->views as $shortcode => $view ) {
      \add_shortcode( $shortcode, function() use ($view) {
        return get_view( $view );
      });
    }
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