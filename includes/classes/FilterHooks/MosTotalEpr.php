<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use MOS\Affiliate\User;
use MOS\Affiliate\Migration\CommissionsMigration;

class MosTotalEpr extends FilterHook {

  protected $hook = 'mos_total_epr';

  public function handler(): string {
    $user = User::current();
    $referrals = $this->get_total_referrals( $user->get_affid() );
    $commissions = $this->get_total_commissions( $user->get_wpid() );
    $epr = $referrals > 0 ? $commissions / $referrals : 0.0;
    $formatted_epr = $this->format( $epr );
    return $formatted_epr;
  }

  private function get_total_referrals( int $affid ): int {
    global $wpdb;
    $user_table = $wpdb->prefix . 'users';
    $table = $wpdb->prefix . 'uap_referrals';
    $query = "SELECT COUNT(refferal_wp_uid) FROM $table WHERE affiliate_id = $affid AND refferal_wp_uid IN (SELECT ID FROM $user_table)";
    $total_commissions = (int) $wpdb->get_var( $query );
    return $total_commissions;
  }

  private function get_total_commissions( int $user_id ): float {
    global $wpdb;
    $table = $wpdb->prefix . CommissionsMigration::TABLE_NAME;
    $query = "SELECT SUM(amount) FROM $table WHERE earner_id = $user_id";
    $total_commissions = (float) $wpdb->get_var( $query );
    return $total_commissions;
  }

  private function format( $total ): string {
    return number_format( $total, 2 );
  }

}