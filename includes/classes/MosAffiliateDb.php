<?php

/**
 * Performs database setters and getters
 */

class MosAffiliateDb {

  public static function instance() {
    $new_instance = new self;
    return $new_instance;
  }


  public function get_campaign_data() {
    global $wpdb;

    // Get affid of current user
    $affid = $this->get_affid();

    // Check if affid is valid
    if ( $affid === false ) {
      return false;
    }

    // Perform SQL lookup
    $table = $wpdb->prefix.'uap_campaigns';
    $query = "SELECT `name`, `visit_count` as clicks, `unique_visits_count` as unique_clicks, `referrals` FROM $table WHERE affiliate_id = $affid";
    $campaign_data = $wpdb->get_results( $query, );

    // Check if campaign data is valid
    if ( empty( $campaign_data ) ) {
      return false;
    }

    return $campaign_data;
  }


  public function get_referrals( array $requested_columns ) {
    global $wpdb;

    $base_table = $wpdb->prefix.'users';
    $affid = $this->get_affid();

    // Check that $affid is valid
    if ( $affid === false ) {
      return false;
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
        'table' => $wpdb->prefix.'usermeta',
        'table_alias' => '_first_name',
        'col' => 'meta_value',
        'col_alias' => 'first_name',
        'join_from' => $base_table.'.id',
        'join_to' => '_first_name.user_id',
        'condition_key' => 'meta_key',
        'condition_value' => "'first_name'",
      ],
      'last_name' => [
        'table' => $wpdb->prefix.'usermeta',
        'table_alias' => '_last_name',
        'col' => 'meta_value',
        'col_alias' => 'last_name',
        'join_from' => $base_table.'.id',
        'join_to' => '_last_name.user_id',
        'condition_key' => 'meta_key',
        'condition_value' => "'last_name'",
      ],
      'level' => [
        'table' => $wpdb->prefix.'usermeta',
        'table_alias' => '_level',
        'col' => 'meta_value',
        'col_alias' => 'level',
        'join_from' => $base_table.'.id',
        'join_to' => '_level.user_id',
        'condition_key' => 'meta_key',
        'condition_value' => "'{$wpdb->prefix}capabilities'",
      ],
      'affid' => [
        'table' => $wpdb->prefix.'uap_affiliates',
        'table_alias' => '_affiliates',
        'col' => 'id',
        'col_alias' => 'affid',
        'join_from' => $base_table.'.id',
        'join_to' => '_affiliates.uid',
      ],
      'sponsor' => [
        'table' => $wpdb->prefix.'uap_affiliate_referral_users_relations',
        'table_alias' => '_sponsor',
        'col' => 'affiliate_id',
        'col_alias' => 'sponsor',
        'join_from' => $base_table.'.id',
        'join_to' => '_sponsor.referral_wp_uid',
        'condition_key' => 'affiliate_id',
        'condition_value' => $affid,
        'mandatory' => true,
      ],
    ];

    // Generate SELECT statement parts
    foreach ( $columns as $column_name => $column ) {
      // Skip if column was not requested by caller
      if ( !in_array( $column_name, $requested_columns ) && !$column['mandatory'] ) {
        continue;
      }

      // Generate alias for column name
      $set_alias = $column['col_alias'] ? "as $column[col_alias]" : '';

      $table = $column['table_alias'] ? $column['table_alias'] : $column['table'];
      $selects[$column_name] = "$table.$column[col] $set_alias";
    }

    // Generate JOIN statement parts
    foreach ( $columns as $column_name => $column ) {
      // Skip if column was not requested by caller
      if ( !in_array( $column_name, $requested_columns ) && !$column['mandatory'] ) {
        continue;
      }

      // Skip if column is from base table
      if ( $column['table'] == $base_table ) {
        continue;
      }

      $joins[$column_name] = "$column[table] as $column[table_alias] ON $column[join_from] = $column[join_to]";
    }

    // Generate WHERE statement parts
    foreach ( $columns as $column_name => $column ) {
      // Skip if column was not requested by caller
      if ( !in_array( $column_name, $requested_columns ) && !$column['mandatory'] ) {
        continue;
      }

      // Skip if column does not need a WHERE statement
      if ( empty( $column['condition_key'] ) ) {
        continue;
      }

      $conditions[$column_name] = "$column[table_alias].$column[condition_key] = $column[condition_value]";
    }

    $selects = implode( ', ', $selects );
    $joins = implode( ' LEFT JOIN ', $joins );
    $conditions = implode ( ' AND ', $conditions );
    $query = "SELECT $selects FROM $base_table LEFT JOIN $joins WHERE $conditions";
    $referrals = $wpdb->get_results( $query );

    return $referrals;
  }
  

  public function get_campaigns() {
    global $wpdb;

    // Get wpid of current user
    $affid = $this->get_affid();

    // If affid not found, return false
    if ( empty( $affid ) ) {
      return false;
    }

    // Perform SQL lookup
    $table = $wpdb->prefix.'uap_campaigns';
    $query = "SELECT DISTINCT name FROM $table WHERE affiliate_id = $affid";
    $campaigns = $wpdb->get_col( $query );

    // If SQL failed, return false
    if ( empty($campaigns) ) {
      return false;
    }

    return $campaigns;
  }


  public function get_affid() {
    global $wpdb;

    // Get wpid of current user
    $wpid = $this->get_wpid();

    // If wpid no found, return false
    if ( empty( $wpid ) ) {
      return false;
    }

    // Perform SQL lookup
    $table = $wpdb->prefix.'uap_affiliates';
    $query = "SELECT id FROM $table WHERE uid = $wpid LIMIT 1";
    $affid = $wpdb->get_var($query);

    // If lookup failed, return false
    if ( empty( $affid ) ) {
      return false;
    }

    return $affid;


  }


  public function get_wpid() {
    $wpid = get_current_user_id();

    if ( empty( $wpid ) ) {
      return false;
    }

    return $wpid;
  }


}