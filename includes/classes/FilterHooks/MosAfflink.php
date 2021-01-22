<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use \MOS\Affiliate\User;

class MosAfflink extends FilterHook {

  protected $hook = 'mos_afflink';

  public function handler(): string {
    $user = User::current();

    if ( !$user->exists() ) {
      return '';
    }

    $uap_var_name = get_option( 'uap_referral_variable', '' );

    if ( empty( $uap_var_name ) ) {
      return '';
    }

    $afflink = home_url( "/?$uap_var_name=$user->user_login" );
    return $afflink;
  }

}