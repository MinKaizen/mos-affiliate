<?php

/**
 * Performs database setters and getters
 */

namespace MOS\Affiliate;

class Database {

  public function get_campaign_data(): array {
    global $wpdb;

    // Get affid of current user
    $affid = $this->get_affid();

    // Check if affid is valid
    if ( empty( $affid ) ) {
      return [];
    }

    // Perform SQL lookup
    $table = $wpdb->prefix.'uap_campaigns';
    $query = "SELECT `name`, `visit_count` as clicks, `unique_visits_count` as unique_clicks, `referrals` FROM $table WHERE affiliate_id = $affid";
    $campaign_data = $wpdb->get_results( $query, \ARRAY_A );

    // Check if campaign data is valid
    if ( empty( $campaign_data ) ) {
      return [];
    }

    foreach( $campaign_data as $index => $campaign ) {
      $campaign_data[$campaign['name']] = $campaign;
      unset( $campaign_data[$index] );
    }

    return $campaign_data;
  }


  public function get_referrals( array $requested_columns ): array {
    global $wpdb;

    $base_table = $wpdb->prefix.'users';
    $affid = $this->get_affid();

    // Check that $affid is valid
    if ( empty( $affid ) ) {
      return [];
    }

    $columns = [
      'id' => [
        'table' => $base_table,
        'col' => 'id',
      ],
      'username' => [
        'table' => $base_table,
        'col' => 'user_login',
      ],
      'email' => [
        'table' => $base_table,
        'col' => 'user_email',
      ],
      'date' => [
        'table' => $base_table,
        'col' => 'user_registered',
      ],
      'first_name' => [
        'table' => "(SELECT user_id, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key='first_name')",
        'table_alias' => '_first_name',
        'col' => 'meta_value',
        'col_alias' => 'first_name',
        'join' => [
          'table' => '',
          'key1' => $base_table.'.id',
          'key2' => '_first_name.user_id',
        ],
      ],
      'last_name' => [
        'table' => "(SELECT user_id, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key='last_name')",
        'table_alias' => '_last_name',
        'col' => 'meta_value',
        'col_alias' => 'last_name',
        'join' => [
          'key1' => $base_table.'.id',
          'key2' => '_last_name.user_id',
        ],
      ],
      'level' => [
        'table' => "(SELECT user_id, meta_value FROM {$wpdb->prefix}usermeta WHERE meta_key='{$wpdb->prefix}capabilities')",
        'table_alias' => '_level',
        'col' => 'meta_value',
        'col_alias' => 'level',
        'join' => [
          'key1' => $base_table.'.id',
          'key2' => '_level.user_id',
        ],
      ],
      'campaign' => [
        'table' => "(SELECT refferal_wp_uid, campaign FROM {$wpdb->prefix}uap_referrals WHERE affiliate_id=$affid)",
        'table_alias' => '_campaign',
        'col' => 'campaign',
        'join' => [
          'key1' => $base_table.'.id',
          'key2' => '_campaign.refferal_wp_uid',
        ],
      ],
      'affid' => [
        'table' => $wpdb->prefix.'uap_affiliates',
        'table_alias' => '_affiliates',
        'col' => 'id',
        'col_alias' => 'affid',
        'join' => [
          'key1' => $base_table.'.id',
          'key2' => '_affiliates.uid',
        ],
      ],
      'sponsor' => [
        'table' => $wpdb->prefix.'uap_affiliate_referral_users_relations',
        'table_alias' => '_sponsor',
        'col' => 'affiliate_id',
        'col_alias' => 'sponsor',
        'join' => [
          'key1' => $base_table.'.id',
          'key2' => '_sponsor.referral_wp_uid',
        ],
        'mandatory' => true,
      ],
    ];

    // Generate query sting parts
    foreach ( $columns as $column_name => $column ) {
      // Skip if column was not requested by caller
      if ( !in_array( $column_name, $requested_columns ) && empty( $column['mandatory'] ) ) {
        continue;
      }

      // Generate SELECT statement parts
      $add_col_alias = !empty( $column['col_alias'] ) ? "as $column[col_alias]" : '';
      $table = !empty( $column['table_alias'] ) ? $column['table_alias'] : $column['table'];
      $selects[$column_name] = "$table.$column[col] $add_col_alias";

      // Generate JOIN statement parts
      if ( $column['table'] !== $base_table ) {
        $joins[$column_name] = "$column[table] as $column[table_alias] ON {$column['join']['key1']} = {$column['join']['key2']}";
      }

    }

    // Prepare SQL query
    $selects = implode( ', ', $selects );
    $joins = implode( ' LEFT JOIN ', $joins );
    $conditions = "{$columns['sponsor']['table_alias']}.{$columns['sponsor']['col']} = $affid";
    $query = "SELECT $selects FROM $base_table LEFT JOIN $joins WHERE $conditions";

    // Execute SQL query
    $referrals = $wpdb->get_results( $query, \ARRAY_A );

    // Check that SQL returned valid result
    if ( empty( $referrals ) ) {
      return [];
    }

    // Final clean up
    foreach( $referrals as &$referral ) {
      // Remove sponsor column
      unset( $referral['sponsor'] );

      // Convert wp_capability to readable level name
      if ( $referral['level'] ) {
        $referral['level'] = $this->wpcap_to_level( $referral['level'] );
      }
    }

    return $referrals;
  }
  

  public function get_campaigns(): array {
    global $wpdb;

    // Get wpid of current user
    $affid = $this->get_affid();

    if ( empty( $affid ) ) {
      return [];
    }

    // Perform SQL lookup
    $table = $wpdb->prefix.'uap_campaigns';
    $query = "SELECT DISTINCT name FROM $table WHERE affiliate_id = $affid";
    $campaigns = $wpdb->get_col( $query );

    if ( empty($campaigns) ) {
      return [];
    }

    return $campaigns;
  }


  public function get_affid(): int {
    global $wpdb;

    // Get wpid of current user
    $wpid = $this->get_wpid();

    // If wpid no found, return 0
    if ( empty( $wpid ) ) {
      return 0;
    }

    // Perform SQL lookup
    $table = $wpdb->prefix.'uap_affiliates';
    $query = "SELECT id FROM $table WHERE uid = $wpid LIMIT 1";
    $affid = $wpdb->get_var($query);

    // If lookup failed, return 0
    if ( empty( $affid ) ) {
      return 0;
    }

    return $affid;
  }


  public function get_wpid(): int {
    $wpid = \get_current_user_id();

    if ( empty( $wpid ) ) {
      return 0;
    }

    return $wpid;
  }


  /**
   * Register a user as a UAP Affiliate
   * If already an affiliate, return the affiliate ID
   * Return the resulting affiliate ID
   * Return 0 if user doesn't exist
   *
   * @param int $id     User WPID
   * @return integer    Affiliate ID
   */
  public function register_affiliate( int $id ): int {
    global $wpdb;

    $user_exists = ( ! empty( \get_userdata( $id ) ) );
    if ( ! $user_exists ) {
      return 0;
    }

    $uap_affiliates_table = $wpdb->prefix . 'uap_affiliates';
    $query = "SELECT id FROM $uap_affiliates_table WHERE uid = $id LIMIT 1";
    $affid = $wpdb->get_var( $query );

    if ( empty( $affid ) ) {
      $columns = [
        'uid' => $id,
        'status' => 1,
      ];
      $format = [
        '%d',
        '%d',
      ];
      $affid = $wpdb->insert( $uap_affiliates_table, $columns, $format );
    }

    return (int) $affid;
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

}