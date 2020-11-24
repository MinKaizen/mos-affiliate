<?php

namespace MOS\Affiliate;

use \WP_CLI;

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

  private $cli_commands = [
    'test_cli_command',
    'reactivate_cli_command',
  ];


  public function __construct() {
    require( PLUGIN_DIR . "/includes/config/caps.php" );
    require( PLUGIN_DIR . "/includes/helpers/utils.php" );
  }


  public function init() {
    $this->load_admin();
    $this->load_scripts();
    $this->register_cli_commands();
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
    if ( !defined( 'WP_CLI' ) || !WP_CLI ) {
      return;  
    }
    foreach ( $this->cli_commands as $cli_command_name ) {
      $class_name = class_name( $cli_command_name, 'CliCommand' );
      $cli_command = new $class_name();
      $cli_command->register();
    }
  }


}