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
    $expected_caps = [
      'access_free' => true,
    ];
    $this->assert_equal( $this->roles['free']['capabilities'], $expected_caps );    
  }
  
  
  public function test_monthly_partner() {
    $this->assert_has_key( $this->roles, 'monthly_partner' );
    $expected_caps = [
      'access_free' => true,
      'access_monthly_partner' => true,
      'display_mis' => true,
    ];
    $this->assert_equal( $this->roles['monthly_partner']['capabilities'], $expected_caps );      
  }


  public function test_yearly_partner() {
    $this->assert_has_key( $this->roles, 'yearly_partner' );
    $expected_caps = [
      'access_free' => true,
      'access_monthly_partner' => true,
      'access_yearly_partner' => true,
      'display_mis' => true,
    ];
    $this->assert_equal( $this->roles['yearly_partner']['capabilities'], $expected_caps );    
  }

}