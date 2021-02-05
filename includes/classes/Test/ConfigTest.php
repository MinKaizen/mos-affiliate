<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use \MOS\Affiliate\Test;

class ConfigTest extends Test {

  const CONFIG_DIR = \MOS\Affiliate\PLUGIN_DIR . 'includes/config/';

  public function test_product_config(): void {
    $product_config = $this->load_json( 'products' );
    $this->assert_not_empty( $product_config, 'product config should be json_decodeable' );

    foreach ( $product_config as $product ) {
      // Mandatory keys:
      $this->assert_not_empty( $product->name, 'Product name should be set' );
      $this->assert_not_empty( $product->access_meta_key, 'Product access_meta_key should be set' );
      $this->assert_not_empty( $product->no_access_url_path, 'Product no_access_url_path should be set' );
      $this->assert_not_empty( $product->price, 'Product price should be set' );

      // Conditionally mandatory keys:
      if ( !empty( $product->trial_period ) ) {
        $this->assert_not_empty( $product->rebill_period, 'If there is a trial period, there must be a rebill period' );
      }
      
      if ( !empty( $product->rebill_period ) ) {
        $this->assert_not_empty( $product->rebill_price, 'If there is a rebill period, there must be a rebill price' );
      }

      if ( !empty( $product->rebill_price ) ) {
        $this->assert_not_empty( $product->rebill_period, 'If there is a rebill price, there must be a rebill period' );
      }

      // Check types
      $this->assert_is_int( $product->cb_id ?? 0, 'Product cb_id must be an int' );
      $this->assert_is_string( $product->name, 'Product name must be a string' );
      $this->assert_is_string( $product->access_meta_key, 'Product access_meta_key must be a string' );
      $this->assert_is_url( \home_url( $product->no_access_url_path ), 'Product  $product must be a valid URL path' );
      $this->assert_is_number( $product->price, 'Product price must be a number' );
      $this->assert_is_int( $product->trial_period ?? 0, 'Product trial_period must be an int' );
      $this->assert_is_number( $product->rebill_price ?? 0, 'Product rebill_price must be a number' );
      $this->assert_is_int( $product->rebill_period ?? 0, 'Product rebill_period must be an int' );

      $this->assert_string_not_contains( $product->access_meta_key, ' ', 'Product access_meta_key must not contain spaces' );
      $this->assert_true( strpos( $product->no_access_url_path, '/' ) === 0, 'Product no_access_url_path must start with a slash (/)' );
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