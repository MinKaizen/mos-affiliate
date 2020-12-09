<?php declare(strict_types=1);

namespace MOS\Affiliate\CliCommand;

use MOS\Affiliate\CliCommand;
use MOS\Affiliate\Test;

use function \WP_CLI\Utils\format_items;

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
  public function run( array $pos_args, array $assoc_args ): void {
    // Note: order matters!
    $this->maybe_delete_users();
    $this->maybe_delete_usermetas();
  }


  private function maybe_delete_users(): void {
    $query_stub = $this->users_query_stub();
    $users = $this->select( $query_stub );
    $this->prompt_delete( 'users', $users, 'user_login, user_email' );
    $this->delete( $query_stub );
  }


  private function users_query_stub(): string {
    global $wpdb;
    $meta_key = Test::TEST_META_KEY;
    $meta_value = Test::TEST_META_VALUE;
    $usermeta_table = $wpdb->prefix . 'usermeta';
    $test_ids = "SELECT user_id FROM $usermeta_table WHERE meta_key = '$meta_key' AND meta_value = '$meta_value'";
    $users_table = $wpdb->prefix . 'users';
    $query_stub = "FROM $users_table WHERE ID in ($test_ids)";
    return $query_stub;
  }


  private function maybe_delete_usermetas(): void {
    $query_stub = $this->usermetas_query_stub();
    $usermetas = $this->select( $query_stub );
    $this->prompt_delete( 'usermetas', $usermetas, 'user_id, meta_key, meta_value' );
    $this->delete( $query_stub );
  }


  private function usermetas_query_stub(): string {
    global $wpdb;
    $users_table = $wpdb->prefix . 'users';
    $user_ids_table = "(SELECT ID FROM $users_table)";
    $table = $wpdb->prefix . 'usermeta';
    $query_stub = "FROM $table WHERE user_id NOT IN $user_ids_table";
    return $query_stub;
  }


  private function select( string $query_stub ): array {
    global $wpdb;
    $query = "SELECT * $query_stub";
    $rows = (array) $wpdb->get_results( $query );
    return $rows;
  }


  private function delete( string $query_stub ): void {

  }


  private function prompt_delete( string $table_stub, array $data, string $columns ): void {
    $count = count( $data );
    
    if ( $count == 0 ) {
      $message = $this->colorize( "$table_stub: nothing to delete.", 'success' );
      $this->get_any_key( $message );
      return;
    }

    $prompt = "$table_stub: $count rows will be deleted. Continue?";
    format_items( 'table', $data, $columns );
    $this->get_confirmation( $prompt, ['confirm_word' => "delete $count"] );
  }


}