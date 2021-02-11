<?php declare(strict_types=1);

namespace MOS\Affiliate;

abstract class ACF {

  abstract function args(): array;

  public function register(): void {
    if( !function_exists( 'acf_add_local_field_group' ) ) {
      return;
    }

    \acf_add_local_field_group( $this->args() );
  }

}