<?php declare(strict_types=1);

namespace MOS\Affiliate\Actions;

use \MOS\Affiliate\AbstractAction;
use \MOS\Affiliate\User;
use \MOS\Affiliate\Product;
use function \get_field;
use function \MOS\Affiliate\url_path_is;
use function \wp_redirect;

class TemplateRedirect_AccessRedirect extends AbstractAction {

  protected $hook = 'template_redirect';

  public function handler(): void {
    if ( !\function_exists( 'get_field' ) ) {
      return;
    }

    $access_level = (string) get_field( 'access_level' );

    if ( !$access_level ) {
      return;
    }

    if ( $access_level === '_free' ) {
      $this->handle_free_redirect();
    } else {
      $this->handle_product_redirect( $access_level );
    }
  }

  private function handle_free_redirect(): void {
    if ( \apply_filters( 'mos_current_user_id', get_current_user_id() ) ) {
      return;
    }

    $fallback = home_url( '/' );
    $redirect = get_field( 'free_no_access_url', 'option' );
    $redirect = $redirect ? $redirect : $fallback;
    wp_redirect( $redirect );
    exit;
  }

  private function handle_product_redirect( string $access_level ): void {
    $product = Product::from_slug( $access_level );
    
    if ( !$product->exists ) {
      return;
    }

    $user = User::current();

    if ( !$user->has_access( $access_level ) && !url_path_is( $product->no_access_url_path ) ) {
      wp_redirect( $product->no_access_url_path );
      die;
    }
  }
}