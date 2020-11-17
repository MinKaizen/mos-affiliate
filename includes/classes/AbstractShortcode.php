<?php

namespace MOS\Affiliate;

abstract class AbstractShortcode {

  protected $slug;

  protected $defaults = [];

  abstract protected function shortcode_action( $args ): string;


  public static function register_all(): void {
    $dir = new \DirectoryIterator( PLUGIN_DIR . '/includes/classes/Shortcode' );

    foreach ($dir as $fileinfo) {
      if ( $fileinfo->isDot() ) {
        continue;
      }
      $class_name = self::shortcode_class_name( $fileinfo->getFilename() );
      $shortcode = new $class_name;
      $shortcode->register();
    }
  }


  public function shortcode_router( $passed_args ): string {
    $combined_args = shortcode_atts( $this->defaults, $passed_args );
    return $this->shortcode_action( $combined_args );
  }

  public function register() {
    \add_shortcode( $this->slug, [ $this, 'shortcode_router' ] );
  }

  public static function shortcode_class_name( string $file_name ): string {
    $extension = '.php';
    $class_name = NS . 'Shortcode\\' . str_replace( $extension, '', $file_name );
    return $class_name;
  }

}