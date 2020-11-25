<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;
use \MOS\Affiliate\Database;

class DatabaseClassTest extends Test {

  
  public function test_get_row() {
    $db = new Database();
    $result = $db->get_row( 'users', ['ID=1'] );
    $result = $db->get_row( 'users', ['ID=1'], ['ID'] );
    $this->assert_is_array( $result );
    $this->assert_equal( $result['ID'], 1, $result );
    $this->assert_false( $result['user_login'] );
  }


}