<?php

namespace MOS\Affiliate;

class AccessRedirect {

  protected $tag;
  protected $redirect_url;
  protected $cap;


  public static function register_all(): void {
    $dir = new \DirectoryIterator( PLUGIN_DIR . '/includes/classes/AccessRedirect' );

    foreach ($dir as $fileinfo) {
      if ( $fileinfo->isDot() ) {
        continue;
      }
      $ar_class_name = self::class_name( $fileinfo->getFilename() );
      $ar_class = new $ar_class_name;
      $ar_class->register();
    }
  }


  public function register(): void {
    \add_action( 'template_redirect', [ $this, 'maybe_redirect' ] );
  }


  public function maybe_redirect(): void {
    if ( !\has_tag( $this->tag ) ) {
      return;
    };

    $user = \wp_get_current_user();
    $has_access = $user->has_cap( $this->cap );
    if ( ! $has_access ) {
      \wp_redirect( \home_url( $this->redirect_url ) );
      exit;
    }
  }


  public static function class_name( string $file_name ): string {
    $extension = '.php';
    $class_name = NS . 'AccessRedirect\\' . str_replace( $extension, '', $file_name );
    return $class_name;
  }

}