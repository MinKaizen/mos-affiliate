<?php declare(strict_types=1);

namespace MOS\Affiliate;

use function \MOS\Affiliate\is_dateable;

class Commission {

  private $id = 0;
  private $date = '';
  private $amount = 0.0;
  private $description = '';
  private $transaction_id = '';
  private $campaign = '';
  private $actor_id = 0;
  private $earner_id = 0;
  private $payout_date = '';
  private $payout_method = '';
  private $payout_address = '';
  private $payout_transaction_id = '';
  private $refund_date = '';
  private $table;


  public function __construct() {
    global $wpdb;
    $this->table = $wpdb->prefix . \MOS\Affiliate\Migration\CommissionsMigration::TABLE_NAME;
  }


  public static function lookup( int $id ): self {
    global $wpdb;
    $table = $wpdb->prefix . \MOS\Affiliate\Migration\CommissionsMigration::TABLE_NAME;
    $query = "SELECT * FROM $table WHERE id = $id";
    $result = $wpdb->get_row( $query, 'ARRAY_A' );
    $commission = self::create_from_array( $result );
    return $commission;
  }


  public static function create_from_stripe_data( array $stripe_data ): self {
    return new self();
  }


  public static function create_from_cb_data( array $cb_data ): self {
    return new self();
  }


  public static function create_from_array( array $data ): self {
    $new_commission = new self();
    $new_commission->id = (int) (!empty($data['id']) ? $data['id'] : 0);
    $new_commission->date = (!empty($data['date']) ? $data['date'] : null);
    $new_commission->amount = (float) (!empty($data['amount']) ? $data['amount'] : 0.0);
    $new_commission->description = (string) (!empty($data['description']) ? $data['description'] : '');
    $new_commission->transaction_id = (string) (!empty($data['transaction_id']) ? $data['transaction_id'] : '');
    $new_commission->campaign = (string) (!empty($data['campaign']) ? $data['campaign'] : '');
    $new_commission->actor_id = (int) (!empty($data['actor_id']) ? $data['actor_id'] : 0);
    $new_commission->earner_id = (int) (!empty($data['earner_id']) ? $data['earner_id'] : 0);
    $new_commission->payout_date = (!empty($data['payout_date']) ? $data['payout_date'] : null);
    $new_commission->payout_method = (string) (!empty($data['payout_method']) ? $data['payout_method'] : '');
    $new_commission->payout_address = (string) (!empty($data['payout_address']) ? $data['payout_address'] : '');
    $new_commission->payout_transaction_id = (string) (!empty($data['payout_transaction_id']) ? $data['payout_transaction_id'] : '');
    $new_commission->refund_date = (!empty($data['refund_date']) ? $data['refund_date'] : null);
    return $new_commission;
  }


  public static function lookup_multi( array $filters ): array {
    return [];
  }


  public function get_id(): int {
    return $this->id;
  }


  public function get_date(): string {
    return $this->date;
  }


  public function get_amount(): float {
    return $this->amount;
  }


  public function get_description(): string {
    return $this->description;
  }


  public function get_transaction_id(): string {
    return $this->transaction_id;
  }


  public function get_campaign(): string {
    return $this->campaign;
  }


  public function get_actor_id(): int {
    return $this->actor_id;
  }


  public function get_earner_id(): int {
    return $this->earner_id;
  }


  public function get_payout_date(): ?string {
    return $this->payout_date;
  }


  public function get_payout_method(): string {
    return $this->payout_method;
  }


  public function get_payout_address(): string {
    return $this->payout_address;
  }


  public function get_payout_transaction_id(): string {
    return $this->payout_transaction_id;
  }


  public function get_refund_date(): ?string {
    return $this->refund_date;
  }


  public function exists(): bool {
    return !empty( $this->id );
  }


  public function is_valid(): bool {
    $conditions = [
      is_dateable( $this->date ),
      is_numeric( $this->amount ),
      $this->amount > 0,
      ! empty( $this->description ),
      is_string( $this->campaign ),
      is_int( $this->earner_id ),
      $this->earner_id > 0,
      $this->payout_date == null || is_dateable( $this->payout_date ),
      $this->payout_method == null || is_string( $this->payout_method ),
      $this->payout_address == null || is_string( $this->payout_address ),
      $this->payout_transaction_id == null || is_string( $this->payout_transaction_id ),
      $this->refund_date == null || is_dateable( $this->refund_date ),
    ];

    $valid = true;
    foreach( $conditions as $condition ) {
      if ( $condition === false ) {
        $valid = false;
        break;
      }
    }
    
    return $valid;
  }


  public function db_insert(): void {
    global $wpdb;
    $table = $wpdb->prefix . \MOS\Affiliate\Migration\CommissionsMigration::TABLE_NAME;
    $columns = [
      'date' => $this->date,
      'amount' => $this->amount,
      'description' => $this->description,
      'transaction_id' => $this->transaction_id,
      'campaign' => $this->campaign,
      'actor_id' => $this->actor_id,
      'earner_id' => $this->earner_id,
      'payout_date' => $this->payout_date,
      'payout_method' => $this->payout_method,
      'payout_address' => $this->payout_address,
      'payout_transaction_id' => $this->payout_transaction_id,
      'refund_date' => $this->refund_date,
    ];
    $formats = [
      'date' => '%s',
      'amount' => '%f',
      'description' => '%s',
      'transaction_id' => '%s',
      'campaign' => '%s',
      'actor_id' => '%d',
      'earner_id' => '%d',
      'payout_date' => '%s',
      'payout_method' => '%s',
      'payout_address' => '%s',
      'payout_transaction_id' => '%s',
      'refund_date' => '%s',
    ];
    $rows_inserted = $wpdb->insert( $table, $columns, $formats );

    if ( $rows_inserted === 1 && is_int( $wpdb->insert_id ) ) {
      $this->id = (int) $wpdb->insert_id;
    }
  }


  public function db_delete(): void {
    global $wpdb;
    $where = ['id' => $this->id];
    $formats = ['id' => '%d'];
    $wpdb->delete( $this->table, $where, $formats );
  }

}