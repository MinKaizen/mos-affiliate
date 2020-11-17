<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\User;

class UserTest extends Test {

  private $username = "_wp_cli_test_user";
  private $wpid;

  public function _before(): void {

  }

  public function main(): void {
    $user = new User();

    // doesn't exist
    $this->assert_true( ! $user->exists(), "User doesn't exist" );

    // constructor
    $this->assert_instanceof( $user, 'MOS\Affiliate\User' );

    // get_wpid()
    $user->id = 42;
    $this->assert_equal( $user->get_wpid(), 42, "get_wpid()", "expected" );

  }


}