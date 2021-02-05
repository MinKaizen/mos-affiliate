<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use \MOS\Affiliate\User;

class MosAccessList extends FilterHook {

  protected $hook = 'mos_access_list';
  protected $args = 2;

  public function handler( $fallback, $user_id=0 ): array {
    $user_id = (int) $user_id;

    if ( $user_id ) {
      $user = User::from_id( $user_id );
    } else {
      $user = User::current();
    }

    $access_list = $user->get_access_list();
    return $access_list;
  }

}