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
      'course_progress' => '',
      'campaign' => 'mos_test_ref_table_1',
    ],
    [
      'date' => '2020-01-02 00:00:00',
      'username' => '', // set in _before()
      'name' => 'mos_test Sally',
      'email' => '', // set in _before()
      'level' => 'monthly_partner',
      'course_progress' => '',
      'campaign' => 'mos_test_ref_table_2',
    ],
    [
      'date' => '2020-01-03 00:00:00',
      'username' => '', // set in _before()
      'name' => 'mos_test Bob',
      'email' => '', // set in _before()
      'level' => 'yearly_partner',
      'course_progress' => '',
      'campaign' => 'mos_test_ref_table_3',
    ],
  ];


  protected function _before(): void {
    foreach ( $this->users as $index => $user ) {
      $this->users[$index]['username'] = ranstr(32);
      $this->users[$index]['email'] = ranstr(20) . '@' . ranstr(5) . '.' . ranstr(5);
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
    }
    
  }

  public function test_controller(): void {
    $controller = new ReferralsTable();
    $vars = $controller->get_vars();
    $this->_injected_user = $this->create_test_user();
    $this->set_user();

    var_dump( $vars );
  }

}