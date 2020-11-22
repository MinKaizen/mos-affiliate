<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

use function wp_roles;

class LevelsExistTest extends Test {

  private $roles;


  public function __construct() {
    $this->roles = wp_roles()->roles;
  }


  public function test_free() {
    $this->assert_has_key( $this->roles, 'free' );
    $this->assert_equal( $this->roles['free']['capabilities']['access_free'], 1 );    
  }
  
  
  public function test_monthly_partner() {
    $this->assert_has_key( $this->roles, 'monthly_partner' );
    $this->assert_equal( $this->roles['monthly_partner']['capabilities']['access_free'], 1 );    
    $this->assert_equal( $this->roles['monthly_partner']['capabilities']['access_monthly_partner'], 1 );    
    $this->assert_equal( $this->roles['monthly_partner']['capabilities']['display_mis'], 1 );    
  }


  public function test_yearly_partner() {
    $this->assert_has_key( $this->roles, 'yearly_partner' );
    $this->assert_equal( $this->roles['yearly_partner']['capabilities']['access_free'], 1 );    
    $this->assert_equal( $this->roles['yearly_partner']['capabilities']['access_monthly_partner'], 1 );    
    $this->assert_equal( $this->roles['yearly_partner']['capabilities']['access_yearly_partner'], 1 );    
    $this->assert_equal( $this->roles['yearly_partner']['capabilities']['display_mis'], 1 );    
  }

}