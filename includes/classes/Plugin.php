<?php

namespace MOS\Affiliate;

use function MOS\Affiliate\class_name;

class Plugin {

  private $shortcodes = [
    'affid_shortcode',
    'campaign_form_shortcode',
    'campaign_list_shortcode',
    'campaign_report_shortcode',
    'email_shortcode',
    'first_name_shortcode',
    'last_name_shortcode',
    'level_shortcode',
    'mis_shortcode',
    'name_shortcode',
    'sponsor_affid_shortcode',
    'sponsor_email_shortcode',
    'sponsor_first_name_shortcode',
    'sponsor_last_name_shortcode',
    'sponsor_level_shortcode',
    'sponsor_mis_shortcode',
    'sponsor_name_shortcode',
    'sponsor_username_shortcode',
    'sponsor_wpid_shortcode',
    'test_shortcode',
    'username_shortcode',
    'wpid_shortcode',
  ];

  private $access_redirects = [
    'free_access_redirect',
    'monthly_partner_access_redirect',
    'yearly_partner_access_redirect',
  ];


  public function __construct() {
    require( PLUGIN_DIR . "/includes/config/caps.php" );
    require( PLUGIN_DIR . "/includes/config/mis.php" );
    require( PLUGIN_DIR . "/includes/config/routes.php" );
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
    foreach ( $this->shortcodes as $shortcode ) {
      $class_name = class_name( $shortcode, 'Shortcode' );
      $shortcode_instance = new $class_name();
      $shortcode_instance->register();
    }
  }


  private function register_access_redirects(): void {
    foreach ( $this->access_redirects as $access_redirect ) {
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