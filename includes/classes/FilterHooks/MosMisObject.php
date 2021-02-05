<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use function \MOS\Affiliate\mis_object;

class MosMisObject extends FilterHook {

  protected $hook = 'mos_mis_object';
  protected $args = 2;

  public function handler( $fallback, $slug ): object {
    return mis_object( $slug );
  }

}