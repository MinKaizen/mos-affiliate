<?php declare(strict_types=1);

namespace MOS\Affiliate\Migration;

use MOS\Affiliate\Migration;

class CommissionsMigration extends Migration {

  protected $table_name = 'mos_commissions';
  protected $columns = [
    'id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT',
    'date date NOT NULL',
    'amount int(10) UNSIGNED NOT NULL',
    'description varchar(90) DEFAULT "" NOT NULL',
    'transaction_id varchar(255) DEFAULT "" NOT NULL',
    'campaign varchar(255) DEFAULT "" NOT NULL',
    'actor_id bigint(20) UNSIGNED NOT NULL',
    'earner_id bigint(20) UNSIGNED NOT NULL',
    'payout_date date NOT NULL',
    'payout_method varchar(100) NOT NULL',
    'payout_address varchar(100) NOT NULL',
    'payout_transaction_id varchar(100) NOT NULL',
    'refund_date date NOT NULL',
    'PRIMARY KEY  (id)',
  ];

}