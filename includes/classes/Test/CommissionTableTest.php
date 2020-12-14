<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\Controller;

class CommissionTableTest extends Test {

  private $commission_data = [
    [
      'date' => '2020-11-05',
      'amount' => '42.00',
      'description' => 'Description 1',
      'transaction_id' => 'asdf1',
      'campaign' => 'facebook1',
      'payout_date' => '2020-04-01',
      'payout_method' => 'Bitcoin1',
      'payout_address' => 'sd54f5s4df6sd5f16sd54fs5d4f',
      'payout_transaction_id' => '54654-5464',
    ],
    [
      'date' => '2020-11-06',
      'amount' => '56.00',
      'description' => 'Description 2',
      'transaction_id' => 'asdf2',
      'campaign' => 'facebook2',
      'payout_date' => '2020-04-02',
      'payout_method' => 'Bitcoin2',
      'payout_address' => 'sd54f5s4asdaf16sd54fs5d4f',
      'payout_transaction_id' => '53354-5464',
    ],
    [
      'date' => '2020-11-07',
      'amount' => '42.00',
      'description' => 'Description 3',
      'transaction_id' => 'asdf3',
      'campaign' => 'facebook3',
      'payout_date' => '2020-04-03',
      'payout_method' => 'Bitcoin3',
      'payout_address' => 'sd54f5s4df6sd5f36sd54fs5d4f',
      'payout_transaction_id' => '54654-5464',
    ],
    [
      'date' => '2021-11-08',
      'amount' => '422.00',
      'description' => 'Description 4',
      'transaction_id' => 'asdf4',
      'campaign' => 'facebook4',
    ],
  ];


  protected function _before(): void {
    $this->_injected_user = $this->create_test_user();
    $this->set_user();
    $actor = $this->create_test_user();

    foreach ( $this->commission_data as $index => $commission ) {
      $this->commission_data[$index]['earner_id'] = $this->_injected_user->ID;
      $this->commission_data[$index]['actor_id'] = $actor->ID;
      $this->commission_data[$index]['display_name'] = $actor->display_name;
      $this->commission_data[$index]['user_email'] = $actor->user_email;
    }

    foreach ( $this->commission_data as $commission ) {
      $this->create_test_commission( $commission );
    }
  }


  public function test_rows(): void {
    $controller = Controller::get_controller( 'commission_table' );;
    $controller_rows = $controller->get_vars()['rows'];
    $commission0 = $this->find_commission_by_transaction_id( $this->commission_data[0]['transaction_id'], $controller_rows );
    $commission1 = $this->find_commission_by_transaction_id( $this->commission_data[1]['transaction_id'], $controller_rows );
    $commission2 = $this->find_commission_by_transaction_id( $this->commission_data[2]['transaction_id'], $controller_rows );
    $commission3 = $this->find_commission_by_transaction_id( $this->commission_data[3]['transaction_id'], $controller_rows );

    foreach ( $this->commission_data[0] as $key => $value ) {
      $this->assert_equal( $value, $commission0[$key], ['testing' => "commission0[$key]", $commission0, $this->commission_data[0]] );
    }

    foreach ( $this->commission_data[1] as $key => $value ) {
      $this->assert_equal( $value, $commission1[$key], ['testing' => "commission1[$key]", $commission1, $this->commission_data[1]] );
    }

    foreach ( $this->commission_data[2] as $key => $value ) {
      $this->assert_equal( $value, $commission2[$key], ['testing' => "commission2[$key]", $commission2, $this->commission_data[2]] );
    }

    $this->assert_empty( $commission3 );

  }


  private function find_commission_by_transaction_id( string $transaction_id, array $commissions ): array {
    foreach ( $commissions as $index => $commission ) {
      if ( isset( $commission['transaction_id'] ) && $commission['transaction_id'] == $transaction_id ) {
        return $commission;
      }
    }
    return [];
  }

}