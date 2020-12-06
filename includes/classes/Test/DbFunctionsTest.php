<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

class DbFunctionsTest extends Test {

  public function test_main(): void {
    echo "This is the DB Functions Test" . PHP_EOL;
  }


  public function test_create_empty_user(): void {
    $user = $this->create_test_user();
    $user = new \WP_User($user);
    $user_from_db = get_user_by( 'id', $user->ID );
    $this->assert_equal( $user, $user_from_db, "Generated user should equal user in db" );
    
    // Delete user
    $this->delete_test_users();
    $user_from_db = get_user_by( 'id', $user->ID );
    $this->assert_false_strict( $user_from_db, "get_user_by() should return false after we delete users" );
  }


}