<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;
use \MOS\Affiliate\Commission;

class CommissionClassTest extends Test {

  private $valid_data = [
    'date' => '2020-12-06',
    'amount' => 50,
    'description' => 'Full info',
    'transaction_id' => '1vG92ClVTga2GIROZ5fEK8JQ1GL7gkCk',
    'campaign' => 'facebook',
    'actor_id' => 64,
    'earner_id' => 144,
    'payout_date' => '2020-04-04',
    'payout_method' => 'Bitcoin',
    'payout_address' => 'sd54f5s4df6sd5f16sd54fs5d4f',
    'payout_transaction_id' => '54654-5464',
    'refund_date' => '2020-12-24',
  ];


  public function test_construct(): void {
    $class_name = '\MOS\Affiliate\Commission';
    $this->assert_class_exists( $class_name );
    $commission = new Commission();
    $this->assert_instanceof( $commission, $class_name );
  }


  public function test_construct_from_array(): void {
    $commission_data = $this->valid_data;
    $commission = Commission::create_from_array( $commission_data );
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


  public function test_is_valid(): void {
    // Valid
    $this->assert_commission_valid();

    // Fields that can be null or coerced
    $this->assert_commission_valid( ['campaign' => null] );
    $this->assert_commission_valid( ['transaction_id' => null] );
    $this->assert_commission_valid( ['actor_id' => null] );
    $this->assert_commission_valid( ['payout_date' => null] );
    $this->assert_commission_valid( ['payout_method' => null] );
    $this->assert_commission_valid( ['payout_address' => null] );
    $this->assert_commission_valid( ['payout_transaction_id' => null] );
    $this->assert_commission_valid( ['refund_date' => null] );

    // Invalid
    $this->assert_commission_invalid( ['date' => '2020-13-13'] );
    $this->assert_commission_invalid( ['date' => '2020-06-32'] );
    $this->assert_commission_invalid( ['date' => '2020-02-31'] );
    $this->assert_commission_invalid( ['amount' => 'string instead of number'] );
    $this->assert_commission_invalid( ['amount' => -1] );
    $this->assert_commission_invalid( ['description' => ''] );
    $this->assert_commission_invalid( ['earner_id' => 'string instead of int'] );
    $this->assert_commission_invalid( ['earner_id' => -1] );
    $this->assert_commission_invalid( ['earner_id' => 0.05] );
    $this->assert_commission_invalid( ['payout_date' => '2020-13-13'] );
    $this->assert_commission_invalid( ['payout_date' => '2020-06-32'] );
    $this->assert_commission_invalid( ['payout_date' => '2020-02-31'] );
    $this->assert_commission_invalid( ['refund_date' => '2020-13-13'] );
    $this->assert_commission_invalid( ['refund_date' => '2020-06-32'] );
    $this->assert_commission_invalid( ['refund_date' => '2020-02-31'] );
  }


  public function test_db_insert(): void {
    $commission = $this->create_test_commission();
    $lookup_commission = Commission::lookup( $commission->get_id() );
    $this->assert_equal( $commission->get_date(), $lookup_commission->get_date() );
    $this->assert_equal( $commission->get_amount(), $lookup_commission->get_amount() );
    $this->assert_equal( $commission->get_description(), $lookup_commission->get_description() );
    $this->assert_equal( $commission->get_transaction_id(), $lookup_commission->get_transaction_id() );
    $this->assert_equal( $commission->get_campaign(), $lookup_commission->get_campaign() );
    $this->assert_equal( $commission->get_actor_id(), $lookup_commission->get_actor_id() );
    $this->assert_equal( $commission->get_earner_id(), $lookup_commission->get_earner_id() );
    $this->assert_equal( $commission->get_payout_date(), $lookup_commission->get_payout_date() );
    $this->assert_equal( $commission->get_payout_method(), $lookup_commission->get_payout_method() );
    $this->assert_equal( $commission->get_payout_address(), $lookup_commission->get_payout_address() );
    $this->assert_equal( $commission->get_payout_transaction_id(), $lookup_commission->get_payout_transaction_id() );
    $this->assert_equal( $commission->get_refund_date(), $lookup_commission->get_refund_date() );
    
    $this->assert_not_equal( $commission->get_id(), 0, 'Commission ID should be populated after insert' );
    $this->assert_true_strict( $commission->exists(), 'Commission should exist() after insert' );

  }


  private function assert_commission_valid( array $edit=[], ...$data ): void {
    $commission_data = $this->valid_data;
    // Apply edits
    if ( !empty( $edit ) ) {
      foreach( $edit as $index => $value ) {
        $commission_data[$index] = $value;
      }
    }

    $commission = Commission::create_from_array( $commission_data );
    $this->assert_true( $commission->is_valid(), $edit );
  }


  private function assert_commission_invalid( array $edit=[], ...$data ): void {
    $commission_data = $this->valid_data;
    // Apply edits
    if ( !empty( $edit ) ) {
      foreach( $edit as $index => $value ) {
        $commission_data[$index] = $value;
      }
    }

    $commission = Commission::create_from_array( $commission_data );
    $this->assert_false( $commission->is_valid(), $edit );
  }


}