<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;

class MosTest extends Shortcode {

  protected $slug = 'mos_test';

  protected $defaults = [
    'att1' => 'default',
    'att2' => 'default',
    'att3' => 'default',
  ];

  public function shortcode_action( $args ): string {
    $result = '';
    $result .= "att1: $args[att1]" . '<br>';
    $result .= "att2: $args[att2]" . '<br>';
    $result .= "att3: $args[att3]" . '<br>';

    return $result;
  }

}