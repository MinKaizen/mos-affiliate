<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;

class TestShortcode extends AbstractShortcode {

  protected $slug = 'mos_test';

  public function shortcode_action( $args ): string {
    return "Hello world!";
  }

}