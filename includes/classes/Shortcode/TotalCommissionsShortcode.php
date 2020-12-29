<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;
use MOS\Affiliate\Migration\CommissionsMigration;

class TotalCommissionsShortcode extends AbstractShortcode {

  protected $slug = 'mos_total_commissions';

  protected $defaults = [
    'username' => '',
  ];

  public function shortcode_action( $args ): string {
    if ( empty( $args['username'] ) ) {
      $user = User::current();
    } else {
      $user = User::from_username( $args['username'] );
    }
    $total = $this->get_total_commissions( $user->get_wpid() );
    $formatted_total = $this->format_total( $total );
    return $formatted_total;
  }

  private function get_total_commissions( int $user_id ): float {
    global $wpdb;
    $table = $wpdb->prefix . CommissionsMigration::TABLE_NAME;
    $query = "SELECT SUM(amount) FROM $table WHERE earner_id = $user_id";
    $total_commissions = (float) $wpdb->get_var( $query );
    return $total_commissions;
  }

  private function format_total( $total ): string {
    return number_format( $total, 0 );
  }

}