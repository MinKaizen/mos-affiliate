<?php declare(strict_types=1);

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;
use MOS\Affiliate\User;

use function MOS\Affiliate\first_non_empty_element;
use function MOS\Affiliate\is_dateable;

class ReferralsTable extends Controller {


  public function __construct() {
    $this->referrals = $this->get_referrals();
  }


  protected function export_referrals(): array {
    return $this->referrals;
  }

  
  protected function export_headers(): array {
    if ( empty( $this->referrals ) ) {
      return [];
    }
    $first_element = first_non_empty_element( $this->referrals );
    $headers = array_keys( $first_element );
    return $headers;
  }


  private function get_referrals(): array {
    $referral_objects = User::current()->get_referrals();
    $referrals = [];

    foreach ( $referral_objects as $user ) {
      $referrals[] = [
        'date' => $this->format_date( $user->user_registered ),
        'username' => $user->get_username(),
        'name' => $user->get_name(),
        'email' => $user->get_email(),
        'level' => $user->get_level(),
        'progress' => $user->get_course_progress( 0 )['formatted'],
        'campaign' => $user->get_campaign(),
      ];
    }

    return $referrals;
  }


  private function format_date( string $date ): string {
    $date_formatted = \DateTime::createFromFormat( 'Y-m-d H:i:s', $date )->format( 'Y-m-d' );
    return $date_formatted;
  }


}