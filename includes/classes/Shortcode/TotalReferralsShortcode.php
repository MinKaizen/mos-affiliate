<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class TotalReferralsShortcode extends AbstractShortcode {

  protected $slug = 'mos_total_referrals';

  protected $defaults = [
    'username' => '',
  ];

  public function shortcode_action( $args ): string {
    if ( empty( $args['username'] ) ) {
      $user = User::current();
    } else {
      $user = User::from_username( $args['username'] );
    }
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