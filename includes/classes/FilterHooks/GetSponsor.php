<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use \MOS\Affiliate\User;

class GetSponsor extends FilterHook {

  protected $hook = 'get_sponsor';

  public function handler(): User {
    return User::current()->sponsor();
  }

}