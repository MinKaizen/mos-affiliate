<?php

namespace MOS\Affiliate;

class MosAffiliatePlugin {


  public function __construct() {
    require( PLUGIN_DIR . "/includes/core/config.php" );
    require( PLUGIN_DIR . "/includes/core/functions.php" );
  }


  public function init() {
    $this->load_dependencies();
    $this->load_scripts();
    $this->register_shortcodes();
  }


  private function load_dependencies() {
    require( PLUGIN_DIR . "/includes/classes/MosAffiliateDb.php" );
    require( PLUGIN_DIR . "/includes/classes/MosAffiliateController.php" );
  }


  private function load_scripts() {
    \add_action( 'wp_enqueue_scripts', function() {
      \wp_enqueue_script( 'mosAffiliate', $this->url.'dist/js/mosAffiliate.js', ['jquery'], '1.0.0', true );
    });
  }


  private function register_shortcodes() {
    // Campaign form
    \add_shortcode( 'mos_uap_campaign_form', function(){
      return get_view('campaign_form');
    });

    // Campaign list
    \add_shortcode( 'mos_uap_campaign_list', function(){
      return get_view('campaign_list');
    });

    // Campaign report
    \add_shortcode( 'mos_uap_campaign_report', function(){
      return get_view('campaign_report');
    });
  }

}