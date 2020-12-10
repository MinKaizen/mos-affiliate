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



  private function format_date( string $date ): string {
    $date_formatted = \DateTime::createFromFormat( 'Y-m-d H:i:s', $date )->format( 'Y-m-d' );
    return $date_formatted;
  }


}