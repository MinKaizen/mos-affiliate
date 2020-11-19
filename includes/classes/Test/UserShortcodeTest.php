<?php

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\User;

class UserShortcodeTest extends Test {

  public function test_main(): void {
    $this->assert_true( 1==1 );
  }


  private function set_user( User $user ): void {
    \add_filter( 'mos_current_user', function() use ($user) {
      return $user;
    } );
  }

  
  private function create_user( string $username ): User {
    // Create User
    $id = \wp_insert_user([
      'user_login' => $username,
    ]);
    $this->assert_is_int( $id, $id );
    $this->db_notice( "$id - user created" );

    // Register user as affiliate
    $db = new Database();
    $success = $db->register_affiliate( $id );
    $this->assert_true_strict( $success );
    $this->db_notice( "$id - registered as affiliate" );

    $user = User::from_id( $id );

    return $user;
  }


}