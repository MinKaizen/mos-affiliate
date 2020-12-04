<?php

/**
 * Performs database setters and getters
 */

namespace MOS\Affiliate;

class Database {

  const RETURN_TYPE_DEFAULT = 'ARRAY_A';


  public function user_has_sponsor( int $id ): bool {
    $sponsor_affid = $this->sponsor_affid( $id );
    return !empty( $sponsor_affid );
  }


  public function sponsor_affid( int $id ): int {
    global $wpdb;
    $table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
    $query = "SELECT affiliate_id FROM $table WHERE referral_wp_uid = $id LIMIT 1";
    $sponsor_affid = (int) $wpdb->get_var( $query );
    return $sponsor_affid;
  }


  /**
   * Convert WP Capability (serialized php array) to slug or nice name (optional)
   *
   * @param string $wpcap               Wordpress capability in php serialized array form
   * @param boolean $use_nice_name      (optional) whether to return a nice name instead of a slug
   * @return string                     Slug or nicename of the level
   */
  private function wpcap_to_level( string $wpcap, bool $use_nice_name=false ): string {
    $unserialized = unserialize( $wpcap );

    if ( empty(array_keys( $unserialized )[0] ) ) {
      return '';
    }

    $level = array_keys( $unserialized )[0];

    // Optionally, return a nice name instead of a slug
    if ( $use_nice_name ) {
      $level = ucwords( str_replace( '_', ' ', $level ) );
    }

    return $level;
  }


  public function get_row( string $table_name_stub, array $conditions=[], array $columns=['*'] ): array {
    global $wpdb;
    $table = $wpdb->prefix . $table_name_stub;
    $conditions_string = implode( ' AND ', $conditions );
    $columns_string = implode( ',', $columns );
    $query = "SELECT $columns_string FROM $table WHERE $conditions_string LIMIT 1";
    $result = $wpdb->get_row( $query, self::RETURN_TYPE_DEFAULT );
    $result = $result ? $result : [];
    return $result;
  }


  public function get_rows( string $table_name_stub, array $conditions=[], array $columns=['*'] ): array {
    global $wpdb;
    $table = $wpdb->prefix . $table_name_stub;
    $conditions_string = implode( ' AND ', $conditions );
    $columns_string = implode( ',', $columns );
    $query = "SELECT $columns_string FROM $table WHERE $conditions_string";
    $result = $wpdb->get_results( $query, self::RETURN_TYPE_DEFAULT );
    $result = $result ? $result : [];
    return $result;
  }

}