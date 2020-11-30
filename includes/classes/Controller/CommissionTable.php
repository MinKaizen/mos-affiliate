<?php

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;
use MOS\Affiliate\Database;

class CommissionTable extends Controller {

  protected $variables = [
    'rows',
    'headers',
    'id',
    'class',
  ];

  private $rows;
  private $tooltips;


  public function __construct() {
    $id = \get_current_user_id();
    $db = new Database();
    $conditions = [
      "earner_id = $id",
      "refund_date IS NULL",
      "payout_date IS NOT NULL",
    ];
    $columns = [
      'date',
      'amount',
      'description',
      'campaign',
      'actor_id',
      'payout_method',
    ];
    $rows = $db->get_rows( 'mos_commissions', $conditions, $columns );
    $this->rows = $this->format_rows( $rows );
    $this->tooltips = $this->generate_tooltips( $rows );
    parent::__construct();
  }


  protected function id(): string {
    return "new_id";
  }


  protected function class(): string {
    return "new-class";
  }


  protected function rows(): array {
    return $this->rows;
  }


  protected function headers(): array {
    $rows = $this->rows();
    $first_row = reset( $rows );
    return array_keys( $first_row );
  }


  protected function tooltips(): array {
    return $this->tooltips;
  }


  private function format_rows( array $rows_raw ): array {
    $rows = [];
    return $rows_raw;
  }


  private function generate_tooltips( array $rows ): array {
    $tooltips = [];
    foreach ( $rows as $index => $row ) {
      $tooltips[$index] = [
        'Date' => empty( $row['payout_date'] ) ? '' : $row['payout_date'],
        'Method' => empty( $row['payout_method'] ) ? '' : $row['payout_method'],
        'Address' => empty( $row['payout_address'] ) ? '' : $row['payout_address'],
        'Transaction ID' => empty( $row['payout_transaction_id'] ) ? '' : $row['payout_transaction_id'],
      ];
    }
    return $tooltips;
  }


}