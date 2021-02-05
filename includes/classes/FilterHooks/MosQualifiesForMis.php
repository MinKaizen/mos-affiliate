<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use \MOS\Affiliate\User;

class MosQualifiesForMis extends FilterHook {

  protected $hook = 'mos_qualifies_for_mis';
  protected $args = 3;

  public function handler( $fallback, $slug, $user_id=0 ): bool {
    if ( $user_id ) {
      $user = User::from_id( $user_id );
    } else {
      $user = User::current();
    }

    if ( !$user->exists() ) {
      return false;
    }

    $qualifies = $user->qualifies_for_mis( $slug );
    return $qualifies;
  }

}