<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

class ConfigTest extends Test {

  const CONFIG_DIR = \MOS\Affiliate\PLUGIN_DIR . 'includes/config/';

  public function test_mis_config(): void {
    $mis_config = $this->load_json( 'mis' );
    var_dump( $mis_config );
  }

  private function load_json( string $name ) {
    $file_path = self::CONFIG_DIR . $name . '.json';
    $this->assert_true( file_exists( $file_path ), ['error' => "Config file '$name' not found", 'file_path' => $file_path] );
    $raw = file_get_contents( $file_path );
    $serialized = json_decode( $raw );
    return $serialized;
  }

}