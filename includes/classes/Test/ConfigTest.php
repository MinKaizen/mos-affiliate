<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

class ConfigTest extends Test {

  const CONFIG_DIR = \MOS\Affiliate\PLUGIN_DIR . 'includes/config/';

  public function test_mis_config(): void {
    $mis_config = $this->load_json( 'mis' );
    $this->assert_not_empty( $mis_config, 'mis config should be json_decodeable' );
    
    foreach ( $mis_config as $mis ) {
      $this->assert_not_empty( $mis->name, 'MIS name should be set' );
      $this->assert_not_empty( $mis->slug, 'MIS slug should be set' );
      $this->assert_not_empty( $mis->meta_key, 'MIS meta_key should be set' );
      $this->assert_not_empty( $mis->default, 'MIS default should be set' );
      $this->assert_not_empty( $mis->link_template, 'MIS link_template should be set' );
      $this->assert_not_empty( $mis->access_level, 'MIS access_level should be set' );

      $this->assert_is_string( $mis->name, 'MIS name should be a string' );
      $this->assert_is_string( $mis->slug, 'MIS slug should be a string' );
      $this->assert_is_string( $mis->meta_key, 'MIS meta_key should be a string' );
      $this->assert_is_string( $mis->default, 'MIS default should be a string' );
      $this->assert_is_url( $mis->link_template, 'MIS link_template should be a valid URL' );
      $this->assert_is_string( $mis->access_level, 'MIS access_level should be a string' );

      $this->assert_string_not_contains( $mis->slug, 'MIS slug should not contain spaces' );
      $this->assert_string_not_contains( $mis->meta_key, 'MIS meta_key should not contain spaces' );
      $this->assert_string_not_contains( $mis->default, 'MIS default should not contain spaces' );
      $this->assert_string_not_contains( $mis->access_level, 'MIS access_level should not contain spaces' );

      $this->assert_less_than_or_equal( strlen( $mis->meta_key ), 32, 'MIS meta_key should not be more than 32 characters long' );
    }
  }

  private function load_json( string $name ) {
    $file_path = self::CONFIG_DIR . $name . '.json';
    $this->assert_true( file_exists( $file_path ), ['error' => "Config file '$name' not found", 'file_path' => $file_path] );
    $raw = file_get_contents( $file_path );
    $serialized = json_decode( $raw );
    return $serialized;
  }

}