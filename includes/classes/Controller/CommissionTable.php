<?php declare(strict_types=1);

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;
use MOS\Affiliate\User;

class CommissionTable extends Controller {

  public function export_rows(): array {
    global $wpdb;
    $id = User::current()->get_wpid();
    $table = $wpdb->prefix . 'mos_commissions';
    $users_table = $wpdb->prefix . 'users';
    $return_type = 'ARRAY_A';
    $query = "SELECT * FROM $table LEFT JOIN (SELECT ID, display_name, user_email FROM $users_table) as users ON users.ID = $table.actor_id WHERE earner_id = $id AND payout_date IS NOT NULL";
    $rows = $wpdb->get_results( $query, $return_type );
    return $rows;
  }

}