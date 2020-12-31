<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

class CbVendorVariablesShortcodeTest extends Test {

  public function test_shortcode(): void {
    $this->_injected_user = $this->create_test_user();
    $this->set_user();
    $this->_injected_sponsor = $this->create_test_user();
    $this->set_sponsor();

    $regex = '/customer_wpid=.+&customer_username=.+&customer_name=.+&customer_email=.+&campaign=.*&sponsor_wpid=.+&sponsor_username=.+&sponsor_name=.+&sponsor_email=.+/';
    $this->assert_true( preg_match( $regex, \do_shortcode( '[mos_cb_vendor_variables]' ) ) );
  }

}