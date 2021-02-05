<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use \MOS\Affiliate\User;

class MosHasAccess extends FilterHook {

  protected $hook = 'mos_has_access';
  protected $args = 3;

  public function handler( $fallback, $slug, $user_id=0 ): bool {
    $user_id = (int) $user_id;
    $slug = (string) $slug;

    if ( $user_id ) {
      $user = User::from_id( $user_id );
    } else {
      $user = User::current();
    }

    return $user->has_access( $slug );
  }

}