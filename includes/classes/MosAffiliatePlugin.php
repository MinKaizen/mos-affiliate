<?php

namespace MOS\Affiliate;

class MosAffiliatePlugin {


  public function init() {
    $this->load_dependencies();
    $this->load_scripts();
    $this->register_shortcodes();
  }


  private function load_dependencies() {
    require_once( PLUGIN_DIR . "/includes/core/config.php" );
    require_once( PLUGIN_DIR . "/includes/classes/MosAffiliateDb.php" );
    require_once( PLUGIN_DIR . "/includes/classes/MosAffiliateController.php" );
  }


  private function load_scripts() {
    \add_action( 'wp_enqueue_scripts', function() {
      \wp_enqueue_script( 'mosAffiliate', $this->url.'dist/js/mosAffiliate.js', ['jquery'], '1.0.0', true );
    });
  }


  private function register_shortcodes() {
    // Campaign form
    \add_shortcode( 'mos_uap_campaign_form', function(){
      return $this->get_view('campaign_form');
    });

    // Campaign list
    \add_shortcode( 'mos_uap_campaign_list', function(){
      return $this->get_view('campaign_list');
    });

    // Campaign report
    \add_shortcode( 'mos_uap_campaign_report', function(){
      return $this->get_view('campaign_report');
    });
  }


  /**
   * Get view html as string
   *
   * @param string  $view_name   Name of the view
   * @return string $view        The view's html as a string
   */
  private function get_view( string $view_name ) {
    $view_file_name = PLUGIN_DIR . "/includes/views/$view_name.php";
    
    // Check if view exits
    if ( !file_exists( $view_file_name ) ) {
      return '';
    }
    
    // Load controller
    $controller = MosAffiliateController::get_controller( $view_name );

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

}