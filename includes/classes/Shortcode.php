<?php

namespace MOS\Affiliate;

abstract class Shortcode {

  protected $slug;

  abstract public function shortcode_action( array $atts ): string;

  public function register() {
    \add_shortcode( $this->slug, [ $this, 'shortcode_action' ] );
  }

}