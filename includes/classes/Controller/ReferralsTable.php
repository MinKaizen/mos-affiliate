<?php declare(strict_types=1);

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;

class ReferralsTable extends Controller {


  public function export_headers(): array {
    return ['name', 'color'];
  }


  public function export_referrals(): array {
    return [
      [
        'name' => 'apple',
        'color' => 'red',
      ],
      [
        'name' => 'banananaaanana',
        'color' => 'yallow',
      ],
    ];
  }


}