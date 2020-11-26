<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

class CommissionTableControllerTest extends Test {

  public function test_temp(): void {
    $user = $this->create_test_user();
    $this->set_user( $user );
    $this->set_user( $user );
    $shortcode = '[mos_wpid]';
    echo "$shortcode: " . \do_shortcode( $shortcode ) . PHP_EOL;
    $shortcode = '[mos_username]';
    echo "$shortcode: " . \do_shortcode( $shortcode ) . PHP_EOL;
  }

}