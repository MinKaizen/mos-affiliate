<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use \MOS\Affiliate\User;
use \MOS\Affiliate\MIS;

class MosAllMis extends FilterHook {

  protected $hook = 'mos_all_mis';

  public function handler() {
    $all_mis = MIS::get_all();
    return $all_mis;
  }

}