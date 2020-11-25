<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\HtmlTable;

use function MOS\Affiliate\get_view;

class CommissionTableShortcode extends AbstractShortcode {

  protected $slug = 'mos_commission_table';

  public function shortcode_action( $args ): string {
    $table_data = [
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
    $table = new HtmlTable( $table_data, ['id'=>'my_id', 'class'=>'my-class'] );
    return get_view('html_table', ['table' => $table]);
  }

}