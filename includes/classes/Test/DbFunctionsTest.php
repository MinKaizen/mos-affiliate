<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

use function \MOS\Affiliate\ranstr;

class DbFunctionsTest extends Test {

  public function test_main(): void {
    echo "This is the DB Functions Test" . PHP_EOL;
  }


  public function test_create_user(): void {
    $user_data = [
      'user_email' => ranstr() . "@test123.testingla",
      'user_login' => ranstr(),
      'first_name' => ranstr(),
      'last_name' => ranstr(),
      'roles' => ['monthly_partner'],
    ];
    $user = $this->create_test_user( $user_data );
    $this->assert_instanceof( $user, '\MOS\Affiliate\User' );
    $user = new \WP_User($user);
    $user_from_db = get_user_by( 'id', $user->ID );
    $this->assert_equal( $user, $user_from_db, "Generated user should equal user in db" );
    
    // Delete user
    $this->delete_test_users();
    $user_from_db = get_user_by( 'id', $user->ID );
    $this->assert_false_strict( $user_from_db, "get_user_by() should return false after we delete users" );
  }


  public function test_create_empty_user(): void {
    $user = $this->create_test_user();
    $this->assert_instanceof( $user, '\MOS\Affiliate\User' );
    $user = new \WP_User($user);
    $user_from_db = get_user_by( 'id', $user->ID );
    $this->assert_equal( $user, $user_from_db, "Generated empty user should equal user in db" );
    
    // Delete user
    $this->delete_test_users();
    $user_from_db = get_user_by( 'id', $user->ID );
    $this->assert_false_strict( $user_from_db, "get_user_by() should return false after we delete users" );
  }


  public function test_create_empty_commission(): void {
    $commission = $this->create_test_commission();
    $this->assert_true( $this->commission_exists( $commission->get_id() ), 'Commission should exist after create' );
    
    // Delete commission
    $this->delete_test_commissions();
    $this->assert_false( $this->commission_exists( $commission->get_id() ), 'Commission should not exist after delete' );
  }


  private function commission_exists( int $id ): bool {
    global $wpdb;
    $table = $wpdb->prefix . \MOS\Affiliate\Migration\CommissionsMigration::TABLE_NAME;
    $query = "SELECT id FROM $table WHERE id = $id";
    $id_from_db = (int) $wpdb->get_var( $query );
    $exists = $id == $id_from_db;
    return $exists;
  }


  public function test_create_commission(): void {
    $commission_data = [
      'date' => '2020-12-06',
      'amount' => '50',
      'description' => 'Full info',
      'transaction_id' => '1vG92ClVTga2GIROZ5fEK8JQ1GL7gkCk',
      'campaign' => 'facebook',
      'actor_id' => '64',
      'earner_id' => '144',
      'payout_date' => '2020-04-04',
      'payout_method' => 'Bitcoin',
      'payout_address' => 'sd54f5s4df6sd5f16sd54fs5d4f',
      'payout_transaction_id' => '54654-5464',
      'refund_date' => '2020-12-24',
    ];
    $commission = $this->create_test_commission( $commission_data );
    $commission_data_from_db = $this->get_commission_array( $commission->get_id() );

    $this->assert_true( $this->commission_exists( $commission->get_id() ), 'Commission should exist after create' );
    $this->assert_equal( $commission_data['date'], $commission_data_from_db['date'] );
    $this->assert_equal( $commission_data['amount'], $commission_data_from_db['amount'] );
    $this->assert_equal( $commission_data['description'], $commission_data_from_db['description'] );
    $this->assert_equal( $commission_data['transaction_id'], $commission_data_from_db['transaction_id'] );
    $this->assert_equal( $commission_data['campaign'], $commission_data_from_db['campaign'] );
    $this->assert_equal( $commission_data['actor_id'], $commission_data_from_db['actor_id'] );
    $this->assert_equal( $commission_data['earner_id'], $commission_data_from_db['earner_id'] );
    $this->assert_equal( $commission_data['payout_date'], $commission_data_from_db['payout_date'] );
    $this->assert_equal( $commission_data['payout_method'], $commission_data_from_db['payout_method'] );
    $this->assert_equal( $commission_data['payout_address'], $commission_data_from_db['payout_address'] );
    $this->assert_equal( $commission_data['payout_transaction_id'], $commission_data_from_db['payout_transaction_id'] );
    $this->assert_equal( $commission_data['refund_date'], $commission_data_from_db['refund_date'] );

    // Delete commission
    $this->delete_test_commissions();
    $this->assert_false( $this->commission_exists( $commission->get_id() ), 'Commission should not exist after delete' );
  }


  private function get_commission_array( int $id ): array {
    global $wpdb;
    $table = $wpdb->prefix . \MOS\Affiliate\Migration\CommissionsMigration::TABLE_NAME;
    $query = "SELECT * FROM $table WHERE id = $id";
    $commission_array = (array) $wpdb->get_row( $query, \ARRAY_A );
    return $commission_array;
  }


}