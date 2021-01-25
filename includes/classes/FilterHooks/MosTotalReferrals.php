<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use \MOS\Affiliate\User;

class MosTotalReferrals extends FilterHook {

  protected $hook = 'mos_total_referrals';

  public function handler(): string {
    $user = User::current();
    $total = $this->get_total_referrals( $user->get_affid() );
    $formatted_total = $this->format_total( $total );
    return $formatted_total;
  }

  private function get_total_referrals( int $affid ): int {
    global $wpdb;
    $user_table = $wpdb->prefix . 'users';
    $table = $wpdb->prefix . 'uap_referrals';
    $query = "SELECT COUNT(refferal_wp_uid) FROM $table WHERE affiliate_id = $affid AND refferal_wp_uid IN (SELECT ID FROM $user_table)";
    $total_commissions = (int) $wpdb->get_var( $query );
    return $total_commissions;
  }

  private function format_total( $total ): string {
    return number_format( $total, 0 );
  }

}