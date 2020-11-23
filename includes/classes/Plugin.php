<?php

namespace MOS\Affiliate;

use function MOS\Affiliate\class_name;

class Plugin {


  public function __construct() {
    require( PLUGIN_DIR . "/includes/config/access_redirects.php" );
    require( PLUGIN_DIR . "/includes/config/caps.php" );
    require( PLUGIN_DIR . "/includes/config/mis.php" );
    require( PLUGIN_DIR . "/includes/config/routes.php" );
    require( PLUGIN_DIR . "/includes/config/shortcodes.php" );
    require( PLUGIN_DIR . "/includes/helpers/utils.php" );
  }


  public function init() {
    $this->load_admin();
    $this->load_scripts();
    $this->register_cli_commands();
    $this->register_routes();
    $this->register_shortcodes();
    $this->register_access_redirects();
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


  private function register_routes(): void {
    \add_action( 'rest_api_init', function() {
      foreach ( ROUTES as $route ) {
        $class_name = class_name( $route, 'Route' );
        $route_instance = new $class_name();
        $route_instance->register();
      }
    } );
  }


  private function register_shortcodes(): void {
    foreach ( SHORTCODES as $shortcode ) {
      $class_name = class_name( $shortcode, 'Shortcode' );
      $shortcode_instance = new $class_name();
      $shortcode_instance->register();
    }
  }


  private function register_access_redirects(): void {
    foreach ( ACCESS_REDIRECTS as $access_redirect ) {
      $class_name = class_name( $access_redirect, 'AccessRedirect' );
      $access_redirect_instance = new $class_name();
      $access_redirect_instance->register();
    }
  }


  private function register_cli_commands(): void {
    if ( !defined( 'WP_CLI' ) || !\WP_CLI ) {
      return;  
    }
    \WP_CLI::add_command( 'mosa', NS . 'CLI' );
  }


}