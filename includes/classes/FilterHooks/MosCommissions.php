<?php declare(strict_types=1);

namespace MOS\Affiliate\FilterHooks;

use \MOS\Affiliate\FilterHook;
use \MOS\Affiliate\User;

class MosCommissions extends FilterHook {

  protected $hook = 'mos_commissions';

  public function handler(): array {
    global $wpdb;
    $id = User::current()->get_wpid();
    $table = $wpdb->prefix . 'mos_commissions';
    $users_table = $wpdb->prefix . 'users';
    $return_type = 'ARRAY_A';
    $query = "SELECT * FROM $table LEFT JOIN (SELECT ID, display_name, user_email FROM $users_table) as users ON users.ID = $table.actor_id WHERE earner_id = $id AND payout_date IS NOT NULL";
    $rows = (array) $wpdb->get_results( $query, $return_type );
    return $rows;
  }

}