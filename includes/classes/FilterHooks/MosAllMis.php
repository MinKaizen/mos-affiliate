<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;

use function \MOS\Affiliate\mis_get_all;
class MosAllMis extends FilterHook {

  protected $hook = 'mos_all_mis';

  public function handler() {
    $all_mis = mis_get_all();
    return $all_mis;
  }

}