<?php declare(strict_types=1);

namespace MOS\Affiliate;

class Plugin {

  private $ok_to_init = false;
  private $pre_init_errors = ['Pre init check did not run!'];


  public function __construct() {
    require( PLUGIN_DIR . "/includes/helpers/utils.php" );
    require( PLUGIN_DIR . '/includes/config/pre_init.php' );
    $this->pre_init_check();
  }


  public function pre_init_check(): void {
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
    if ( ! $this->ok_to_init ) {
      $this->print_pre_init_errors();
      return;
    }

    if ( defined( 'WP_CLI' ) && WP_CLI ) {
      \WP_CLI::add_command( 'mosa', '\MOS\Affiliate\CLI' );
    }

    // Set timezone
    date_default_timezone_set( \get_option('timezone_string') );

    $this->register_cpts();
    $this->register_classes_from_folder( 'Shortcode' );
    $this->register_classes_from_folder( 'Routes' );
    $this->register_classes_from_folder( 'ActionHooks' );
    $this->register_classes_from_folder( 'FilterHooks' );
  }


  private function print_pre_init_errors(): void {
    foreach ( $this->pre_init_errors as $error ) {
      $this->admin_notice( $error, 'error' );
    }
  }


  private function register_cpts() {
    include( PLUGIN_DIR . 'includes/CPT.php' );
  }


  private function register_classes_from_folder( string $relative_namespace, string $function_name='register' ): void {
    $autoload_root_path = PLUGIN_DIR . 'includes/classes/';
    $dir = new \DirectoryIterator( $autoload_root_path . $relative_namespace );
    foreach ( $dir as $fileinfo ) {
      if ( !$fileinfo->isDot() && !$fileinfo->isDir() ) {
        $class_name = NS . $relative_namespace . '\\' . str_replace( '.php', '', $fileinfo->getFilename() );
        $instance = new $class_name();
        $instance->$function_name();
      }
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