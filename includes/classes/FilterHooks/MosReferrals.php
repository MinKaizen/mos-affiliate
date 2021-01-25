<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use \MOS\Affiliate\User;

class MosReferrals extends FilterHook {

  protected $hook = 'mos_referrals';

  public function handler(): array {
    return $this->get_referrals();
  }

  private function get_referrals(): array {
    $referral_objects = User::current()->get_referrals();
    $referrals = [];

    foreach ( $referral_objects as $user ) {
      if ( !($user instanceof User) || !$user->exists() ) {
        continue;
      }
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