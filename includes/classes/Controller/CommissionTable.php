<?php declare(strict_types=1);

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;
use MOS\Affiliate\Database;
use MOS\Affiliate\User;

class CommissionTable extends Controller {

  private $rows;
  private $tooltips;


  public function __construct() {
    $id = User::current()->get_wpid();
    $db = new Database();
    $conditions = [
      "earner_id = $id",
      "refund_date IS NULL",
      "payout_date IS NOT NULL",
    ];
    $rows = $db->get_rows( 'mos_commissions', $conditions );
    $this->rows = $this->format_rows( $rows );
    $this->tooltips = $this->generate_tooltips( $rows );
  }


  protected function export_rows(): array {
    return $this->rows;
  }


  protected function export_headers(): array {
    $rows = $this->export_rows();
    $first_row = reset( $rows );
    $headers = empty( $first_row ) ? [] : array_keys( $first_row );
    return $headers;
  }


  protected function export_tooltips(): array {
    return $this->tooltips;
  }


  private function format_rows( array $rows_raw ): array {
    $rows = [];
    foreach ( $rows_raw as $row ) {
      $rows[] = [
        'date' => empty( $row['date'] ) ? '' : $row['date'],
        'amount' => empty( $row['amount'] ) ? '' : '$' . $row['amount'],
        'name' => empty( $row['actor_id'] ) ? '' : ucwords( strtolower( User::from_id( (int) $row['actor_id'])->get_name() ) ),
        'email' => empty( $row['actor_id'] ) ? '' : strtolower( User::from_id( (int) $row['actor_id'])->get_email() ),
        'product' => empty( $row['description'] ) ? '' : $row['description'],
        'campaign' => empty( $row['campaign'] ) ? '' : $row['campaign'],
        'payment' => empty( $row['payout_method'] ) ? '' : $row['payout_method'],
      ];
    }
    return $rows;
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