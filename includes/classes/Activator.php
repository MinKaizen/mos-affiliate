<?php declare(strict_types=1);

namespace MOS\Affiliate;

class Activator {

  public static function activate(): void {
    Upgrader::upgrade();
    Level::register_all_levels();
    $activator = new self();
    $activator->add_index_to_uap_referrals();
  }


  public function add_index_to_uap_referrals(): void {
    global $wpdb;
    $table = $wpdb->prefix . 'uap_referrals';
    $columns = 'refferal_wp_uid';
    $index_name = 'uap_referrals_wpid_index';
    
    if ( $this->db_index_exists( $table, $index_name ) ) {
      return;
    }

    $query = "CREATE INDEX $index_name ON $table ($columns)";
    $wpdb->query( $query );
  }


  public function db_index_exists( string $table, string $index ): bool {
    global $wpdb;
    $query = "SELECT COUNT(1) indexExists
              FROM INFORMATION_SCHEMA.STATISTICS
              WHERE table_schema=DATABASE()
              AND table_name='$table'
              AND index_name='$index'";
    $exists = $wpdb->get_var( $query ) == '1';
    return $exists;
  }

}