<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use \MOS\Affiliate\User;
use function \get_current_user_id;

class MosGetSponsor extends FilterHook {

  protected $hook = 'mos_get_sponsor';
  protected $args = 2;

  public function handler( $fallback, $id=0  ): User {
    if ( !$id ) {
      $id = \apply_filters( 'mos_current_user_id', get_current_user_id() );
    }

    if ( !$id ) {
      return User::current()->sponsor();
    }

    return User::from_id( $id )->sponsor();
  }

}