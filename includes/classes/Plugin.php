<?php declare(strict_types=1);

namespace MOS\Affiliate;

use function MOS\Affiliate\class_name;

class Plugin {

  private $ok_to_init = false;
  private $pre_init_errors = ['Pre init check did not run!'];

  private $cli_commands = [
    'test_cli_command',
    'reactivate_cli_command',
  ];


  public function __construct() {
    require( PLUGIN_DIR . "/includes/config/caps.php" );
    require( PLUGIN_DIR . "/includes/helpers/utils.php" );
  }


  public function pre_init_check(): void {
    require( PLUGIN_DIR . '/includes/config/pre_init.php' );

    $this->ok_to_init = true;
    $this->pre_init_errors = [];

    if ( version_compare( phpversion(), PHP_VERSION_MIN, '<' ) ) {
      $this->ok_to_init = false;
      $this->pre_init_errors[] = 'PHP version ' . PHP_VERSION_MIN . ' required. ' . phpversion() . ' found';
    }
    
    foreach ( FUNCTION_DEPENDENCIES as $function ) {
      if ( !function_exists( $function ) ) {
        $this->ok_to_init = false;
        $this->pre_init_errors[] = "Function $function required but not found";
      }
    }

    foreach ( CLASS_DEPENDENCIES as $class ) {
      if ( !class_exists( $class ) ) {
        $this->ok_to_init = false;
        $this->pre_init_errors[] = "Class $class required but not found";
      }
    }
  }


  public function init(): void {
    if ( $this->ok_to_init ) {
      $this->load_admin();
      $this->load_scripts();
      $this->register_cli_commands();
      $this->register_shortcodes();
      $this->register_access_redirects();
    } else {
      $this->print_pre_init_errors();
    }
  }


  private function print_pre_init_errors(): void {
    foreach ( $this->pre_init_errors as $error ) {
      $this->admin_notice( $error, 'error' );
    }
  }


  private function load_admin(): void {
    $admin = new Admin();
    $admin->init();
  }


  private function load_scripts(): void {
    \add_action( 'wp_enqueue_scripts', function() {
      \wp_enqueue_script( 'mosAffiliate', PLUGIN_URL . 'dist/js/mosAffiliate.js', ['jquery'], '1.0.0', true );
    });
  }


  private function register_shortcodes(): void {
    $dir = new \DirectoryIterator( PLUGIN_DIR . 'includes/classes/Shortcode/' );
    foreach ( $dir as $fileinfo ) {
      if ( !$fileinfo->isDot() ) {
        $class_name = NS . 'Shortcode\\' . str_replace( '.php', '', $fileinfo->getFilename() );
        $shortcode_instance = new $class_name();
        $shortcode_instance->register();
      }
    }
  }


  private function register_access_redirects(): void {
    $dir = new \DirectoryIterator( PLUGIN_DIR . 'includes/classes/AccessRedirect/' );
    foreach ( $dir as $fileinfo ) {
      if ( !$fileinfo->isDot() ) {
        $class_name = NS . 'AccessRedirect\\' . str_replace( '.php', '', $fileinfo->getFilename() );
        $access_redirect_instance = new $class_name();
        $access_redirect_instance->register();
      }
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


  private function admin_notice( string $message, string $type='info' ): void {
    $full_message = "<strong>".PLUGIN_NAME.": </strong>" . $message;
    $notice_class = "notice notice-$type";
    $html_message = "<div class='$notice_class'><p>$full_message</p></div>";
    \add_action( 'admin_notices', function() use ($html_message) {
      echo $html_message;
    } );
  }


}