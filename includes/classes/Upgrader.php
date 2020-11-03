<?php

namespace MOS\Affiliate;

class Upgrader {

  public static function upgrade() {
    global $wpdb;

    $sql = [];
    $charset_collate = $wpdb->get_charset_collate();
    $plugin_table_prefix = $wpdb->prefix . 'mos_';
    
    // mis table
    $table_name = $plugin_table_prefix . 'mis';
    $sql[] = "CREATE TABLE $table_name (
      id int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
      slug varchar(20) NOT NULL,
      name varchar(63) NOT NULL,
      default_value varchar(63) NOT NULL,
      url varchar(255) NOT NULL,
      KEY slug (slug)
    ) $charset_collate;";
    
    // // another table
    // $table_name = $plugin_table_prefix . 'table_name';
    // $sql[] = ...

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    \dbDelta( $sql );
  }

}