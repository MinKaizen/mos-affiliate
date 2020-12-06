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
    $user = new \WP_User($user);
    $user_from_db = get_user_by( 'id', $user->ID );
    $this->assert_equal( $user, $user_from_db, "Generated empty user should equal user in db" );
    
    // Delete user
    $this->delete_test_users();
    $user_from_db = get_user_by( 'id', $user->ID );
    $this->assert_false_strict( $user_from_db, "get_user_by() should return false after we delete users" );
  }


}