<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

use function MOS\Affiliate\mis_default_value;
use function \MOS\Affiliate\mis_object;

class MisFunctionsTest extends Test {

  // NOTE: All tests relate to the mis.json config file

  public function test_constructor(): void {
    // Valid
    $gr_mis = mis_object( 'gr' );
    $this->assert_true( $gr_mis->exists, 'MIS for gr should exist' );
    
    $cb_mis = mis_object( 'cb' );
    $this->assert_true( $cb_mis->exists, 'MIS for cb should exist' );
    
    $cm_mis = mis_object( 'cm' );
    $this->assert_true( $cm_mis->exists, 'MIS for cm should exist' );

    // Invalid
    $something_else_mis = mis_object( 'something_else' );
    $this->assert_false( $something_else_mis->exists, 'MIS for something_else should not exist' );
    
    $blah_mis = mis_object( 'blah' );
    $this->assert_false( $blah_mis->exists, 'MIS for blah should not exist' );
  }

  public function test_default_value(): void {
    $this->assert_equal( mis_default_value( 'gr' ), 'htlcb' );
    $this->assert_equal( mis_default_value( 'cb' ), 'htlcb' );
    $this->assert_equal( mis_default_value( 'cm' ), 'htlcb' );
  }

}