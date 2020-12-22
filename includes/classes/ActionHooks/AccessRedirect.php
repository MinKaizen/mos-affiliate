<?php declare(strict_types=1);

namespace MOS\Affiliate\ActionHooks;

use \MOS\Affiliate\ActionHook;
use \MOS\Affiliate\User;
use \MOS\Affiliate\Product;
use function \get_field;
use function \MOS\Affiliate\url_path_is;
use function \wp_redirect;

class AccessRedirect extends ActionHook {

  protected $hook = 'template_redirect';

  public function handler(): void {
    if ( !\function_exists( 'get_field' ) ) {
      return;
    }

    $access_level = (string) get_field( 'access_level' );
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