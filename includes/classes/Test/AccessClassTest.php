<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;
use \MOS\Affiliate\Product;
use \MOS\Affiliate\Access;

class AccessClassTest extends Test {

  public function test_access_date(): void {
    $expected = [
      'monthly_partner' => [
        Access::TXN_BILL => date( 'Y-m-d', time() + \DAY_IN_SECONDS * 33 ),
        Access::TXN_TEST_BILL => date( 'Y-m-d', time() + \DAY_IN_SECONDS * 33 ),
        Access::TXN_SALE => date( 'Y-m-d', time() + \DAY_IN_SECONDS * 9 ),
        Access::TXN_TEST_SALE => date( 'Y-m-d', time() + \DAY_IN_SECONDS * 9 ),
      ],
      'yearly_partner' => [
        Access::TXN_SALE => Access::EVERGREEN_DATE,
        Access::TXN_TEST_SALE => Access::EVERGREEN_DATE,
      ],
      'lifetime_partner' => [
        Access::TXN_SALE => Access::EVERGREEN_DATE,
        Access::TXN_TEST_SALE => Access::EVERGREEN_DATE,
      ],
      'coaching' => [
        Access::TXN_SALE => Access::EVERGREEN_DATE,
        Access::TXN_TEST_SALE => Access::EVERGREEN_DATE,
      ],
    ];

    foreach ( $expected as $slug => $dates ) {
      $product = Product::from_slug( $slug );
      foreach ( $dates as $transaction => $expected_date ) {
        $actual_date = Access::access_date( $product, $transaction );
        $this->assert_equal( $actual_date, $expected_date, [
          'expected' => $expected_date,
          'actual' => $actual_date,
          'transaction' => $transaction,
          'product' => $product,
        ] );
      }
    }
  }

  public function test_grant_remove_access(): void {
    $user = $this->create_test_user();

    $products = Product::get_all();

    foreach ( $products as $product ) {
      $this->assert_false( $user->has_access( $product->slug ), "User should not have access to $product->slug by default" );
      Access::grant_access( $user->ID, $product, Access::TXN_SALE );
      $this->assert_true( $user->has_access( $product->slug ), "User should have access to $product->slug after grant_access()" );
      Access::remove_access( $user->ID, $product );
      $this->assert_false( $user->has_access( $product->slug ), "User should not have access to $product->slug after remove_access()" );
    }
  }

}
