<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;
use \MOS\Affiliate\HtmlTable;

class HtmlTableClassTest extends Test {

  private $table_array = [
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
  private $table_options = [
    'id' => 'table-id',
    'class' => 'table-class',
  ];

  public function test_construct(): void {
    $table = new HtmlTable( $this->table_array );
    $this->assert_instanceof( $table, '\MOS\Affiliate\HtmlTable' );
  }


  public function test_rows(): void {
    $table = new HtmlTable( $this->table_array );
    $this->assert_equal( $this->table_array, $table->rows() );
  }


  public function test_headers(): void {
    $table = new HtmlTable( $this->table_array );
    $expected_headers = [
      'Name',
      'Email',
      'Username',
      'Something Snake Case',
    ];
    $this->assert_equal( $table->headers(), $expected_headers );
  }


  public function test_options(): void {
    $table = new HtmlTable( $this->table_array, $this->table_options );
    $this->assert_equal( $table->id(), $this->table_options['id'] );
    $this->assert_equal( $table->class(), $this->table_options['class'] );
  }


}