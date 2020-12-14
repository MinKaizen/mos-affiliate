<?php declare(strict_types=1);

namespace MOS\Affiliate\Migration;

use MOS\Affiliate\Migration;

class ProductsMigration extends Migration {

  const TABLE_NAME = 'mos_products';

  protected $table_name = self::TABLE_NAME;
  protected $columns = [
    'id int(4) UNSIGNED NOT NULL AUTO_INCREMENT',
    'cb_item_id int(4) UNSIGNED NOT NULL ',
    'cb_transaction_type varchar(40) DEFAULT "" NOT NULL',
    'name varchar(64) NOT NULL',
    'price float(10, 2) UNSIGNED NOT NULL',
    'PRIMARY KEY  (id)',
    'KEY mos_products__for_lookup (cb_item_id, cb_transaction_type)',
  ];

}