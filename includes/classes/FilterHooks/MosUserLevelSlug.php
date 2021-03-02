<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use \MOS\Affiliate\User;
use function \get_current_user_id;

class MosUserLevelSlug extends FilterHook {

  protected $hook = 'mos_user_level_slug';
  protected $args = 2;

  public function handler( $fallback, $id=0 ): string {
    if ( !$id ) {
      $id = get_current_user_id();
    }

    if ( !$id ) {
      return User::current()->get_level_slug();
    }

    return User::from_id( $id )->get_level_slug();
  }

}