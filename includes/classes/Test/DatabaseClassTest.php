<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;
use \MOS\Affiliate\Database;

use function \site_url;

class DatabaseClassTest extends Test {

  
  public function test_get_row(): void {
    $db = new Database();
    $result = $db->get_row( 'users', ['ID=1'], ['ID'] );
    $this->assert_is_array( $result );
    $this->assert_equal( count($result), 1, 'Result should only contain 1 column because only one column was filtered' );
    $this->assert_equal( $result['ID'], 1, $result, 'Result should show ID of admin user' );
    $this->assert_false( $result['user_login'], 'Result should not include columns not included in the filter' );
  }


  public function test_get_multiple_rows(): void {
    $db = new Database();
    $result = $db->get_rows( 'options', ['1'], ['option_name', 'option_value'] );
    $this->assert_is_array( $result );
    $this->assert_greater_than( count($result), 1, 'Result should contain more than 1 element' );

    $site_url_expected = site_url();
    $site_url_found = '';
    $option_row = [];
    foreach ( $result as $option ) {
      if ( $option['option_name'] == 'siteurl' ) {
        $option_row = $option;
        $site_url_found = $option['option_value'];
      }
    }
    $this->assert_equal( $site_url_found, $site_url_expected, $option_row );
  }


}