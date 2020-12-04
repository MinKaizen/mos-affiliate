<?php

/**
 * Performs database setters and getters
 */

namespace MOS\Affiliate;

class Database {

  const RETURN_TYPE_DEFAULT = 'ARRAY_A';

  /**
   * Register a user as a UAP Affiliate
   * If user doesn't exist, return false
   * If already an affiliate, return true
   * On Success, return true
   *
   * @param int $id     User WPID
   * @return integer    Affiliate ID
   */
  public function register_affiliate( int $id ): bool {
    global $wpdb;

    // Check if user exists
    $user_exists = ( ! empty( \get_userdata( $id ) ) );
    if ( ! $user_exists ) {
      return false;
    }

    // Check if affiliate ID already exists
    $uap_affiliates_table = $wpdb->prefix . 'uap_affiliates';
    $query = "SELECT id FROM $uap_affiliates_table WHERE uid = $id LIMIT 1";
    $affid = $wpdb->get_var( $query );
    if ( ! empty( $affid ) ) {
      return true;
    }

    // Insert new affiliate
    $columns = [
      'uid' => $id,
      'status' => 1,
    ];
    $format = [
      '%d',
      '%d',
    ];
    $rows_inserted = $wpdb->insert( $uap_affiliates_table, $columns, $format );
    if ( $rows_inserted === 0 || $rows_inserted === false ) {
      return false;
    }

    return true;
  }


  public function add_sponsor( int $user_id, int $sponsor_id ): bool {
    if ( ! $this->user_exists( $user_id )) {
      return false;
    }

    if ( ! $this->user_exists( $sponsor_id ) ) {
      return false;
    }

    if ( ! $this->user_is_affiliate( $user_id ) ) {
      return false;
    }

    if ( ! $this->user_is_affiliate( $sponsor_id ) ) {
      return false;
    }

    if ( $this->user_has_sponsor( $user_id ) ) {
      return false;
    }

    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
    $data = [
      'affiliate_id' => $this->user_affid( $sponsor_id ),
      'referral_wp_uid' => $user_id,
    ];
    $formats = [
      'affiliate_id' => '%d',
      'referral_wp_uid' => '%d',

    ];
    
    $rows_inserted = $wpdb->insert( $table, $data, $formats );
    $success = ($rows_inserted === 1);
    return $success;
  }


  public function user_is_affiliate( int $id ): bool {
    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliates';
    $query = "SELECT id FROM $table WHERE uid = $id LIMIT 1";
    $affid = (int) $wpdb->get_var( $query );

    if ( empty( $affid ) ) {
      return false;
    } else {
      return true;
    }
  }


  public function user_affid( int $id ): int {
    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliates';
    $query = "SELECT id FROM $table WHERE uid = $id LIMIT 1";
    $affid = (int) $wpdb->get_var( $query );

    return $affid;
  }


  public function user_exists( int $id ): bool {
    $user = \get_user_by( 'id', $id );
    return !empty($user);
  }


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