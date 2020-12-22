<?php declare(strict_types=1);

namespace MOS\Affiliate\ActionHooks;

use \MOS\Affiliate\ActionHook;

class LoadACF extends ActionHook {

  const ACF_CONFIG = \MOS\Affiliate\PLUGIN_DIR . '/includes/config/acf.json';

  protected $hook = 'admin_init';

  public function __construct() {
    if ( !\file_exists( self::ACF_CONFIG ) ) {
      throw new \Error('Cannot load ACF Config at ' . self::ACF_CONFIG );
    }
  }

  public function handler(): void {
    if ( !function_exists( 'acf_add_local_field_group' ) ) {
      return;
    }

    foreach ( $this->acf_data() as $field_group ) {
      \acf_add_local_field_group( $field_group );
    }
  }

  private function acf_data(): array {
    $json = (string) \file_get_contents( self::ACF_CONFIG );
    $data = (array) \json_decode( $json, true );
    return $data;
  }

}