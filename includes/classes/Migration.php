<?php declare(strict_types=1);

namespace MOS\Affiliate;

abstract class Migration {

  protected $table_name;
  protected $columns;
  protected $db_prefix;
  protected $charset_collate;


  public function __construct() {
    global $wpdb;
    $this->db_prefix = $wpdb->prefix;
    $this->charset_collate = $wpdb->get_charset_collate();
  }

  
  public function sql(): string {
    $columns = implode( ',' . PHP_EOL, $this->columns );
    $table_name = $this->db_prefix . $this->table_name;
    $sql = "CREATE TABLE $table_name (
      $columns
    ) $this->charset_collate;";
    return $sql;
  }


  public function run(): void {
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    \dbDelta( $this->sql() );
  }

}