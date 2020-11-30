<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\Controller;

use function \MOS\Affiliate\ranstr;

class CommissionTableTest extends Test {


  public function __construct() {
    $this->_injected_user = $this->create_test_user();

    $actor_data = [
      'first_name' => 'Jill',
      'last_name' => 'Smith',
      'user_email' => ranstr() . "@" . ranstr() . ".com",
    ];

    $this->actor = $this->create_test_user( $actor_data );

    $commission_data = [
      [
        'date' => '2020-11-05',
        'amount' => 42,
        'description' => 'Description 1',
        'transaction_id' => 'asdf1',
        'campaign' => 'facebook',
        'actor_id' => $this->actor->ID,
        'earner_id' => $this->_injected_user->ID,
        'payout_date' => '2020-04-01',
        'payout_method' => 'Bitcoin',
        'payout_address' => 'sd54f5s4df6sd5f16sd54fs5d4f',
        'payout_transaction_id' => '54654-5464',
      ],
      [
        'date' => '2020-11-06',
        'amount' => 56,
        'description' => 'Description 2',
        'transaction_id' => 'asdf2',
        'campaign' => 'facebook2',
        'actor_id' => $this->actor->ID,
        'earner_id' => $this->_injected_user->ID,
        'payout_date' => '2020-04-02',
        'payout_method' => 'Bitcoin',
        'payout_address' => 'sd54f5s4asdaf16sd54fs5d4f',
        'payout_transaction_id' => '53354-5464',
      ],
      [
        'date' => '2020-11-07',
        'amount' => 42,
        'description' => 'Description 3',
        'transaction_id' => 'asdf3',
        'campaign' => 'facebook',
        'actor_id' => $this->actor->ID,
        'earner_id' => $this->_injected_user->ID,
        'payout_date' => '2020-04-03',
        'payout_method' => 'Bitcoin',
        'payout_address' => 'sd54f5s4df6sd5f36sd54fs5d4f',
        'payout_transaction_id' => '54654-5464',
      ],
      [
        'date' => '2021-11-08',
        'amount' => 422,
        'description' => 'Description 4',
        'transaction_id' => 'asdf4',
        'campaign' => 'facebook4',
        'actor_id' => $this->actor->ID,
        'earner_id' => $this->_injected_user->ID,
      ],
      [
        'date' => '2001-11-08',
        'amount' => 12,
        'description' => 'Description 5',
        'transaction_id' => 'asdf5',
        'campaign' => 'facebook5',
        'actor_id' => $this->actor->ID,
        'earner_id' => $this->_injected_user->ID,
        'payout_date' => '2020-04-05',
        'payout_method' => 'Bitcoin',
        'payout_address' => 'sd54f55465f16sd54fs5d4f',
        'payout_transaction_id' => '54-5464',
        'refund_date' => '2020-12-24',
      ],
    ];

    foreach ( $commission_data as $commission ) {
      $this->create_test_commission( $commission );
    }
  }


  public function test_rows(): void {
    $controller = Controller::get_controller( 'commission_table' );;
    $vars = $controller->get_vars();
    $expected_rows = [
      [
        'date' => '2020-11-05',
        'amount' => '$42.00',
        'name' => 'Jill Smith',
        'email' => $this->actor->user_email,
        'product' => 'Description 1',
        'campaign' => 'facebook',
        'payment' => 'Bitcoin',
      ],
      [
        'date' => '2020-11-06',
        'amount' => '$56.00',
        'name' => 'Jill Smith',
        'email' => $this->actor->user_email,
        'product' => 'Description 2',
        'campaign' => 'facebook2',
        'payment' => 'Bitcoin',
      ],
      [
        'date' => '2020-11-07',
        'amount' => '$42.00',
        'name' => 'Jill Smith',
        'email' => $this->actor->user_email,
        'product' => 'Description 3',
        'campaign' => 'facebook',
        'payment' => 'Bitcoin',
      ],
    ];

    function sort_by_date( array $commission1, array $commission2 ): int {
      return $commission1['date'] <=> $commission2['date'];
    }

    foreach ( $expected_rows as $commission ) {
      $this->assert_has_key( $commission, 'date' );
    }
    
    foreach ( $vars['rows'] as $commission ) {
      $this->assert_has_key( $commission, 'date' );
    }

    $expected_rows = usort( $expected_rows, __NAMESPACE__ . '\\sort_by_date' );
    $vars['rows'] = usort( $vars['rows'], __NAMESPACE__ . '\\sort_by_date' );

    $this->assert_equal( $expected_rows, $vars['rows'] );
  }


  public function test_tooltips(): void {
    $controller = Controller::get_controller( 'commission_table' );;
    $vars = $controller->get_vars();
    $expected_tooltips = [
      [
        'Date' => '2020-04-01',
        'Method' => 'Bitcoin',
        'Address' => 'sd54f5s4df6sd5f16sd54fs5d4f',
        'Transaction ID' => '54654-5464',
      ],
      [
        'Date' => '2020-04-02',
        'Method' => 'Bitcoin',
        'Address' => 'sd54f5s4asdaf16sd54fs5d4f',
        'Transaction ID' => '53354-5464',
      ],
      [
        'Date' => '2020-04-03',
        'Method' => 'Bitcoin',
        'Address' => 'sd54f5s4df6sd5f36sd54fs5d4f',
        'Transaction ID' => '54654-5464',
      ],
    ];

    $sort_by_date_function = function ( array $commission1, array $commission2 ): int {
      return $commission1['Date'] <=> $commission2['Date'];
    };

    foreach ( $expected_tooltips as $commission ) {
      $this->assert_has_key( $commission, 'Date' );
    }
    
    foreach ( $vars['tooltips'] as $commission ) {
      $this->assert_has_key( $commission, 'Date' );
    }

    $expected_tooltips = usort( $expected_tooltips, $sort_by_date_function );
    $vars['tooltips'] = usort( $vars['tooltips'], $sort_by_date_function );

    $this->assert_equal( $expected_tooltips, $vars['tooltips'] );
  }


}