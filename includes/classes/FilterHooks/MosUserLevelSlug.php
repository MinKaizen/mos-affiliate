<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use \MOS\Affiliate\User;

class MosUserLevelSlug extends FilterHook {

  protected $hook = 'mos_user_level_slug';

  public function handler(): string {
    return User::current()->get_level_slug();
  }

}