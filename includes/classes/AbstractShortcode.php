<?php

namespace MOS\Affiliate;

abstract class AbstractShortcode {

  protected $slug;

  protected $defaults = [];

  abstract protected function shortcode_action( $args ): string;


  public function shortcode_router( $passed_args ): string {
    $combined_args = shortcode_atts( $this->defaults, $passed_args );
    return $this->shortcode_action( $combined_args );
  }


  public function register() {
    \add_shortcode( $this->slug, [ $this, 'shortcode_router' ] );
  }


}