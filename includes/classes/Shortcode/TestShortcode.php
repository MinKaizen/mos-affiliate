<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class TestShortcode extends AbstractShortcode {

  protected $slug = 'mos_test';

  public function shortcode_action( $args ): string {
    return "Hello world!";
  }

}