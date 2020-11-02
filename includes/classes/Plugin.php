<?php

namespace MOS\Affiliate;

class Plugin {

  private $views = [
    // shortcode name => view name
    'mos_campaign_form' => 'campaign_form',
    'mos_campaign_list' => 'campaign_list',
    'mos_campaign_report' => 'campaign_report',
  ];

  private $shortcodes = [
    // shortcode name => callable
    'mos_wpid' => '\get_current_user_id',
  ];


  public function __construct() {
    require( PLUGIN_DIR . "/includes/core/config.php" );
    require( PLUGIN_DIR . "/includes/core/functions.php" );
  }


  public function init() {
    $this->load_scripts();
    $this->register_views();
    $this->register_shortcodes();
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
    foreach ( $this->shortcodes as $shortcode => $function ) {
      \add_shortcode( $shortcode, $function );
    }
  }

}