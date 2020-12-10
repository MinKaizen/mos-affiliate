<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\Controller\ReferralsTable;

class ReferralsTableTest extends Test {

  public function test_controller(): void {
    $controller = new ReferralsTable();
    $vars = $controller->get_vars();
    $this->_injected_user = $this->create_test_user();
    $this->set_user();

    var_dump( $vars );
  }

}