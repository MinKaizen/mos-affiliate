<?php

namespace MOS\Affiliate;

class Plugin {

  private $routes = [
    'hello_test_route',
  ];


  public function __construct() {
    require( PLUGIN_DIR . "/includes/config/caps.php" );
    require( PLUGIN_DIR . "/includes/config/levels.php" );
    require( PLUGIN_DIR . "/includes/config/mis.php" );
    require( PLUGIN_DIR . "/includes/helpers/utils.php" );
  }


  public function init() {
    $this->load_admin();
    $this->load_scripts();
    AbstractShortcode::register_all();
    AccessRedirect::register_all();
    if ( defined( 'WP_CLI' ) && WP_CLI ) {
      \WP_CLI::add_command( 'mosa', NS . 'CLI' );
    }
    $this->register_routes();
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
      foreach ( $this->routes as $route ) {
        $class_name = AbstractRoute::class_name( $route );
        $route_instance = new $class_name();
        $route_instance->register();
      }
    } );
  }


}