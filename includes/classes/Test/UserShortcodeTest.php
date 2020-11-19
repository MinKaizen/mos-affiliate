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

  
}