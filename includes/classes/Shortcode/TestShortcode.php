<?php

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\Shortcode;
use MOS\Affiliate\User;

class TestShortcode extends Shortcode {

  protected $slug = 'mos_test';

  public function shortcode_action( $args ): string {
    return "Hello world!";
  }

}