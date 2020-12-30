<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;
use \MOS\Affiliate\MIS;

class MisClassTest extends Test {

  // NOTE: All tests relate to the mis.json config file

  public function test_constructor(): void {
    // Valid
    $gr_mis = new MIS( 'gr' );
    $this->assert_true( $gr_mis->exists, 'MIS for gr should exist' );
    
    $cb_mis = new MIS( 'cb' );
    $this->assert_true( $cb_mis->exists, 'MIS for cb should exist' );
    
    $cm_mis = new MIS( 'cm' );
    $this->assert_true( $cm_mis->exists, 'MIS for cm should exist' );

    // Invalid
    $something_else_mis = new MIS( 'something_else' );
    $this->assert_false( $something_else_mis->exists, 'MIS for something_else should not exist' );
    
    $blah_mis = new MIS( 'blah' );
    $this->assert_false( $blah_mis->exists, 'MIS for blah should not exist' );
  }

  public function test_default_value(): void {
    $this->assert_equal( MIS::default_value_for( 'gr' ), 'htlcb' );
    $this->assert_equal( MIS::default_value_for( 'cb' ), 'htlcb' );
    $this->assert_equal( MIS::default_value_for( 'cm' ), 'htlcb' );
  }

}