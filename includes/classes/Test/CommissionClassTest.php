<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;
use \MOS\Affiliate\Commission;

class CommissionClassTest extends Test {


  public function test_construct(): void {
    $class_name = '\MOS\Affiliate\Commission';
    $this->assert_class_exists( $class_name );
    $commission = new Commission();
    $this->assert_instanceof( $commission, $class_name );
  }


  public function test_construct_from_array(): void {
    $commission_data = [
      'date' => '2020-15-06',
      'amount' => 50,
      'description' => 'Some description',
      'transaction_id' => '1vG92ClVTga2GIROZ5fEK8JQ1GL7gkCk',
      'campaign' => 'facebook',
      'actor_id' => 64,
      'earner_id' => 144,
      'payout_date' => '',
      'payout_method' => '',
      'payout_address' => '',
      'payout_transaction_id' => '',
      'refund_date' => '',
    ];
    $commission = Commission::from_array( $commission_data );
    $this->assert_equal( $commission->get_date(), $commission_data['date']);
    $this->assert_equal( $commission->get_amount(), $commission_data['amount']);
    $this->assert_equal( $commission->get_description(), $commission_data['description']);
    $this->assert_equal( $commission->get_transaction_id(), $commission_data['transaction_id']);
    $this->assert_equal( $commission->get_campaign(), $commission_data['campaign']);
    $this->assert_equal( $commission->get_actor_id(), $commission_data['actor_id']);
    $this->assert_equal( $commission->get_earner_id(), $commission_data['earner_id']);
    $this->assert_equal( $commission->get_payout_date(), $commission_data['payout_date']);
    $this->assert_equal( $commission->get_payout_method(), $commission_data['payout_method']);
    $this->assert_equal( $commission->get_payout_address(), $commission_data['payout_address']);
    $this->assert_equal( $commission->get_payout_transaction_id(), $commission_data['payout_transaction_id']);
    $this->assert_equal( $commission->get_refund_date(), $commission_data['refund_date']);
  }


}