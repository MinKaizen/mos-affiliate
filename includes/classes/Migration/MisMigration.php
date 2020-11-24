<?php declare(strict_types=1);

namespace MOS\Affiliate\Migration;

use MOS\Affiliate\Migration;

class MisMigration extends Migration {

  protected $table_name = 'mis';
  protected $columns = [
    'id int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY',
    'slug varchar(20) NOT NULL',
    'name varchar(63) NOT NULL',
    'default_value varchar(63) NOT NULL',
    'url varchar(255) NOT NULL',
    'KEY slug (slug)',
  ];

}