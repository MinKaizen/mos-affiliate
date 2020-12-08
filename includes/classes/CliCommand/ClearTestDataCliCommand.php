<?php declare(strict_types=1);

namespace MOS\Affiliate\CliCommand;

use MOS\Affiliate\CliCommand;
use MOS\Affiliate\Test;
use \WP_CLI;
use \WP_User_Query;

use function MOS\Affiliate\ranstr;
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
  }


  private function maybe_delete_users(): void {
    $users = $this->get_users();
    $this->prompt_delete( 'users', $users, 'user_login, user_email' );
    $this->delete_users( $users );
  }


  private function get_users(): array {
    $args = [
      'meta_key' => Test::TEST_META_KEY,
      'meta_value' => Test::TEST_META_VALUE,
    ];
    $user_query = new WP_User_Query( $args );
    $user_query->query();
    $results = $user_query->get_results();
    return $results;
  }


  private function delete_users(): void {
    WP_CLI::success( 'X users deleted (not really...)' );
  }




  private function select( string $query_stub ): array {
    global $wpdb;
    $table = $wpdb->prefix . $table_stub;
    $query = "SELECT * $query_stub";
    $rows = (array) $wpdb->get_results( $query );
    return $rows;
  }


  private function prompt_delete( string $term_plural, array $data, string $columns ): void {
    $count = count( $data );
    $prompt = "$count $term_plural will be deleted. Continue?";
    format_items( 'table', $data, $columns );
    $this->get_confirmation( $prompt, ranstr(4), 'success' );
  }


}