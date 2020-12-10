<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\Controller\ReferralsTable;

use function MOS\Affiliate\ranstr;

class ReferralsTableTest extends Test {

  private $users = [
    [
      'date' => '2020-01-01 00:00:00',
      'username' => '', // set in _before()
      'name' => 'mos_test John',
      'email' => '', // set in _before()
      'level' => 'free',
      'progress' => '',
      'campaign' => 'mos_test_ref_table_1',
    ],
    [
      'date' => '2020-01-02 00:00:00',
      'username' => '', // set in _before()
      'name' => 'mos_test Sally',
      'email' => '', // set in _before()
      'level' => 'monthly_partner',
      'progress' => '',
      'campaign' => 'mos_test_ref_table_2',
    ],
    [
      'date' => '2020-01-03 00:00:00',
      'username' => '', // set in _before()
      'name' => 'mos_test Bob',
      'email' => '', // set in _before()
      'level' => 'yearly_partner',
      'progress' => '',
      'campaign' => 'mos_test_ref_table_3',
    ],
  ];

  private $users_formatted = [
    [
      'date' => '2020-01-01',
      'username' => '', // set in _before()
      'name' => 'mos_test John',
      'email' => '', // set in _before()
      'level' => 'Free Member',
      'progress' => '',
      'campaign' => 'mos_test_ref_table_1',
    ],
    [
      'date' => '2020-01-02',
      'username' => '', // set in _before()
      'name' => 'mos_test Sally',
      'email' => '', // set in _before()
      'level' => 'Monthly Partner',
      'progress' => '',
      'campaign' => 'mos_test_ref_table_2',
    ],
    [
      'date' => '2020-01-03',
      'username' => '', // set in _before()
      'name' => 'mos_test Bob',
      'email' => '', // set in _before()
      'level' => 'Yearly Partner',
      'progress' => '',
      'campaign' => 'mos_test_ref_table_3',
    ],
  ];

  private $expected_headers = [
    'date',
    'username',
    'name',
    'email',
    'level',
    'progress',
    'campaign',
  ];


  protected function _before(): void {
    foreach ( $this->users as $index => $user ) {
      $this->users[$index]['username'] = ranstr(32);
      $this->users[$index]['email'] = ranstr(20) . '@' . ranstr(5) . '.' . ranstr(5);
    }

    // Also store values in the formatted version
    foreach ( $this->users as $index => $user ) {
      $this->users_formatted[$index]['username'] = $this->users[$index]['username'];
      $this->users_formatted[$index]['email'] = $this->users[$index]['email'];
    }

    $this->_injected_user = $this->create_test_user();
    $this->set_user();

    foreach ( $this->users as $user ) {
      $user_data = [
        'user_registered' => $user['date'],
        'user_login' => $user['username'],
        'user_email' => $user['email'],
        'display_name' => $user['name'],
      ];

      $db_user = $this->create_test_user( $user_data );
      $db_user->set_role( $user['level'] );
      $this->create_test_referral( $db_user->ID, $this->_injected_user->ID, $user['campaign'] );

      // // Debug
      // $debug = [
      //   'date' => $db_user->user_registered,
      //   'username' => $db_user->user_login,
      //   'name' => $db_user->display_name,
      //   'email' => $db_user->user_email,
      //   'level' => $db_user->get_level(),
      //   'progress' => $db_user->get_progress( 0 ),
      //   'campaign' => $db_user->get_campaign(),
      // ];
      // print_r( $debug );
    }
    
  }

  public function test_controller(): void {
    $controller = new ReferralsTable();
    $vars = $controller->get_vars();
    $referrals_actual = $vars['referrals'];
    $headers_actual = $vars['headers'];

    $this->_injected_user = $this->create_test_user();
    $this->set_user();

    $sort_by_date_function = function ( array $commission1, array $commission2 ): int {
      return $commission1['date'] <=> $commission2['date'];
    };

    usort( $referrals_actual, $sort_by_date_function );
    $this->assert_equal_strict( $this->users_formatted, $referrals_actual );
    $this->assert_equal_strict( $this->expected_headers, $headers_actual );
  }

}