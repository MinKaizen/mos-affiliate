<?php declare(strict_types=1);

namespace MOS\Affiliate\CliCommand;

use MOS\Affiliate\CliCommand;
use MOS\Affiliate\Test;

use function \WP_CLI\Utils\format_items;
use function \MOS\Affiliate\expand_merge_tags;

class ClearTestDataCliCommand extends CliCommand {

  protected $command = 'clear_test_data';

  /**
   * To delete:
   *  users where usermeta test exists
   *  usermeta where user_id doesn't exist
   *  uap_affiliate where uid doesn't exist
   *  uap_referral where uid doesn't exist
   *  uap_referral where affid doesn't exist
   *  uap_visits where uid doesn't exist
   *  uap_visits where affid doesn't exist
   *  posts where postmeta test exists
   *  commissions where description is mos_test
   */
  private $tables = [
    'users' => [
      'name' => '%PREFIX%users',
      'debug_columns' => 'user_login, user_email',
      'where_clause' => 'ID in (SELECT user_id FROM %PREFIX%usermeta WHERE meta_key = "%USERS_TEST_META_KEY%" AND meta_value = "%USERS_TEST_META_VALUE%" )',
    ],
    'usermeta' => [
      'name' => '%PREFIX%usermeta',
      'debug_columns' => 'user_id, meta_key, meta_value',
      'where_clause' => 'user_id NOT IN (SELECT ID FROM %PREFIX%users)',
    ],
    'uap_affiliates' => [
      'name' => '%PREFIX%uap_affiliates',
      'debug_columns' => 'id, start_data',
      'where_clause' => 'uid NOT IN (SELECT ID FROM %PREFIX%users)',
    ],
    'uap_referrals' => [
      'name' => '%PREFIX%uap_referrals',
      'debug_columns' => 'campaign, source, date',
      'where_clause' => 'refferal_wp_uid NOT IN (SELECT ID FROM %PREFIX%users) OR affiliate_id NOT IN (SELECT id FROM %PREFIX%uap_affiliates)',
    ],
    'uap_visits' => [
      'name' => '%PREFIX%uap_visits',
      'debug_columns' => 'ref_hash, campaign_name, ip, url',
      'where_clause' => 'referral_id NOT IN (SELECT ID FROM %PREFIX%users) OR affiliate_id NOT IN (SELECT id FROM %PREFIX%uap_affiliates)',
    ],
    'posts' => [
      'name' => '%PREFIX%posts',
      'debug_columns' => 'post_author, post_title',
      'where_clause' => '',
    ],
    'mos_commissions' => [
      'name' => '%PREFIX%mos_commissions',
      'debug_columns' => 'amount, description, campaign',
      'where_clause' => '',
    ],
  ];


  public function run( array $pos_args, array $assoc_args ): void {
    $this->init();
    $this->maybe_delete( 'users' );
    $this->maybe_delete( 'usermeta' );
    $this->maybe_delete( 'uap_affiliates' );
    $this->maybe_delete( 'uap_referrals' );
    $this->maybe_delete( 'uap_visits' );
  }


  private function init(): void {
    global $wpdb;

    $merge_tags = [
      '%PREFIX%' => $wpdb->prefix,
      '%USERS_TEST_META_KEY%' => Test::TEST_META_KEY,
      '%USERS_TEST_META_VALUE%' => Test::TEST_META_VALUE,
    ];

    $this->tables = expand_merge_tags( $this->tables, $merge_tags );
  }


  private function maybe_delete( string $table_stub ): void {
    $this->table_stub_is_valid_or_exit( $table_stub );
    $table = $this->tables[$table_stub]['name'];
    $where_clause = $this->tables[$table_stub]['where_clause'];
    $debug_columns = $this->tables[$table_stub]['debug_columns'];
    $results = $this->get_results( $table, $where_clause );

    if ( count( $results ) == 0 ) {
      $message = $this->colorize( "$table_stub: nothing to delete.", 'success' );
      $this->get_any_key( $message );
      return;
    }

    if ( $this->prompt_delete( $table_stub, $results, $debug_columns ) ) {
      $this->delete( $table, $where_clause );
    } else {
      \WP_CLI::line( "Skipped..." );
    }
  }


  private function table_stub_is_valid_or_exit( string $table_stub ): void {
    if ( !isset( $this->tables[$table_stub] ) ) {
      $this->exit( "table stub invalid: tables[$table_stub] not set", 'error' );
    }

    if ( !isset( $this->tables[$table_stub]['name'] ) ) {
      $this->exit( "table stub invalid: tables[$table_stub][name] not set", 'error' );
    }

    if ( !isset( $this->tables[$table_stub]['where_clause'] ) ) {
      $this->exit( "table stub invalid: tables[$table_stub][where_clause] not set", 'error' );
    }

    if ( !isset( $this->tables[$table_stub]['debug_columns'] ) ) {
      $this->exit( "table stub invalid: tables[$table_stub][debug_columns] not set", 'error' );
    }
  }


  private function get_results( string $table, string $where_clause ) {
    global $wpdb;
    $query = "SELECT * FROM $table WHERE $where_clause";
    \WP_CLI::line( "SQL: $query" );
    $results = (array) $wpdb->get_results( $query );
    return $results;
  }


  private function delete( string $table, string $where_clause ): void {
    global $wpdb;
    $query = "DELETE FROM $table WHERE $where_clause";
    \WP_CLI::line( "SQL: $query" );
    $rows_deleted = (int) $wpdb->query( $query );
    $this->get_any_key( "$rows_deleted rows deleted." );
  }


  private function prompt_delete( string $table_stub, array $data, string $columns ): bool {
    $count = count( $data );
    $prompt = "$table_stub: $count rows will be deleted. Continue?";
    format_items( 'table', $data, $columns );
    $confirm = $this->get_confirmation( $prompt, ['confirm_word' => "delete $count", 'color' => 'danger'] );
    return $confirm;
  }


}