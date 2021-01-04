<?php declare(strict_types=1);

namespace MOS\Affiliate;

use \Error;

class Product {

  const CONFIG = PLUGIN_DIR . '/includes/config/products.json';
  const ACCESS_LEWAY_PERIOD = 2;
  const FAILED_PAYMENT_RETRY_PERIOD = 14;
  # See this https://support.clickbank.com/hc/en-us/articles/220364187-Selling-Recurring-Products#h_e150423c-bf1b-456b-931d-cd7dd852633f
  
  public $exists = false;

  public $cb_id;
  public $name;
  public $slug;
  public $access_meta_key;
  public $no_access_url_path;
  
  # price is also the initial price for recurring products
  public $price; 

	public $has_trial_period;
	public $trial_price;
  public $trial_period;
	public $trial_access_duration;
  
  public $is_recurring;
  public $rebill_price;
	public $rebill_period;
  public $rebill_access_duration;
  
  

  public function __construct() {
    if ( !file_exists( self::CONFIG ) ) {
      $error_msg = "Could not find products config at: " . self::CONFIG;
      echo $error_msg;
      throw new Error( $error_msg );
    }
  }


	public static function from_slug( string $slug ): self {
    $all_products = json_decode( file_get_contents( self::CONFIG ) );
    $product_data = $all_products->$slug ?? new \stdClass();
    $new_product = new self();

    if ( $product_data ) {
      $new_product = self::load_data( $new_product, $product_data );
    }

    return $new_product;
  }


	public static function from_cb_id( int $cb_id ): self {
		$products = json_decode( file_get_contents( self::CONFIG ) );
    $new_product = new self();

    foreach ( $products as $product_data ) {
      $product_cb_id = $product_data->cb_id ?? null;
      if ( $product_cb_id === $cb_id ) {
        $new_product = self::load_data( $new_product, $product_data );
        break;
      }
    }

    return $new_product;
  }
  

  public static function get_all(): array {
    $products_raw = json_decode( file_get_contents( self::CONFIG ) );
    $products = [];

    foreach ( $products_raw as $product_slug => $product_data ) {
      $products[$product_slug] = new self();
      $products[$product_slug] = self::load_data( $products[$product_slug], $product_data );
    }
    
    return $products;
  }
  

  private static function load_data( self $product, object $data ): self {
    $product->exists = true;

		$product->cb_id = $data->cb_id ?? null;
    $product->name = $data->name ?? null;
    $product->slug = $data->slug ?? null;
    $product->access_meta_key = $data->access_meta_key ?? null;
    $product->no_access_url_path = $data->no_access_url_path ?? '/404';

    $product->price = $data->price ?? null;

    $product->has_trial_period = isset( $data->trial_period );
    if ( $product->has_trial_period ) {
      $product->trial_price = $data->price ?? null;
      $product->trial_period = $data->trial_period ?? null;
      $product->trial_access_duration = $data->trial_period + self::ACCESS_LEWAY_PERIOD ?? null ;
    }
    
    $product->is_recurring = $data->rebill_price ?? null && $data->rebill_period ?? null ? true : false;
    if ( $product->is_recurring ) {
      $product->rebill_price = $data->rebill_price ?? null;
      $product->rebill_period = $data->rebill_period ?? null;
      $product->rebill_access_duration = $data->rebill_period + self::ACCESS_LEWAY_PERIOD ?? null;
    }

    return $product;
  }

}