<?php

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;

class CommissionTable extends Controller {

  protected $variables = [
    'rows',
    'headers',
    'id',
    'class',
  ];

  private $rows;


  public function __construct() {
    parent::__construct();
    $this->table = [
      [
        'name' => 'Martin',
        'email' => 'martin@gmail.com',
        'username' => 'martin145',
        'something_snake_case' => 'martin145',
      ],
      [
        'name' => 'Tom',
        'email' => 'tom@gmail.com',
        'username' => 'tomhanks',
        'something_snake_case' => 'tomhanks',
      ],
      [
        'name' => 'Hayasa',
        'email' => 'hayasa@gmail.com',
        'username' => 'hayasaka<3',
        'something_snake_case' => 'hayasaka<3',
      ],
    ];
  }


  protected function rows(): array {
    if ( !empty( $this->rows ) ) {
      return $this->rows;
    }
    $this->rows = [
      [
        'name' => 'Martin',
        'email' => 'martin@gmail.com',
        'username' => 'martin145',
        'something_snake_case' => 'martin145',
      ],
      [
        'name' => 'Tom',
        'email' => 'tom@gmail.com',
        'username' => 'tomhanks',
        'something_snake_case' => 'tomhanks',
      ],
      [
        'name' => 'Hayasa',
        'email' => 'hayasa@gmail.com',
        'username' => 'hayasaka<3',
        'something_snake_case' => 'hayasaka<3',
      ],
    ];
    return $this->rows;
  }


  protected function headers(): array {
    $rows = $this->rows();
    $first_row = reset( $rows );
    return array_keys( $first_row );
  }


  protected function id(): string {
    return "new_id";
  }


  protected function class(): string {
    return "new-class";
  }

}