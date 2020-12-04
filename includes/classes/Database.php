<?php

/**
 * Performs database setters and getters
 */

namespace MOS\Affiliate;

class Database {

  const RETURN_TYPE_DEFAULT = 'ARRAY_A';


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