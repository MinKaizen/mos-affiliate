<?php declare(strict_types=1);

namespace MOS\Affiliate;

use \Error;

class Product {

  const CONFIG = PLUGIN_DIR . '/includes/config/products.json';
  const FAILED_PAYMENT_RETRY_PERIOD = 14;
  # See this https://support.clickbank.com/hc/en-us/articles/220364187-Selling-Recurring-Products#h_e150423c-bf1b-456b-931d-cd7dd852633f
  
  public $exists = false;

  public $cb_id;
  public $name;
  public $access_meta_key;
  public $access_redirect_url;
  
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
    $product_data = nullsafe_get( $all_products, $slug );
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
      if ( nullsafe_get( $product_data, 'cb_id' ) === $cb_id ) {
        $new_product = self::load_data( $new_product, $product_data );
        break;
      }
    }

    return $new_product;
  }
  

  private static function load_data( self $product, object $data ): self {
    $product->exists = true;

		$product->cb_id = nullsafe_get( $data, 'cb_id' );
    $product->name = nullsafe_get( $data, 'name' );
    $product->access_meta_key = nullsafe_get( $data, 'access_meta_key' );
    $product->access_redirect_url = nullsafe_get( $data, 'access_redirect_url', '/404' );

    $product->price = nullsafe_get( $data, 'price' );

    $product->has_trial_period = nullsafe_get( $data, 'trial_period' ) ? true : false;
    $product->trial_price = $product->has_trial_period ? nullsafe_get( $data, 'price' ) : null;
    $product->trial_period = $product->has_trial_period ? nullsafe_get( $data, 'trial_period' ): null;
    $product->trial_access_duration = $product->has_trial_period ? nullsafe_get( $data, 'trial_period' ) + self::FAILED_PAYMENT_RETRY_PERIOD: null;
    
    $product->is_recurring = nullsafe_get( $data, 'rebill_price' ) && nullsafe_get( $data, 'rebill_period' ) ? true : false;
    $product->rebill_price = $product->is_recurring ? nullsafe_get( $data, 'rebill_price' ) : null;
    $product->rebill_period = $product->is_recurring ? nullsafe_get( $data, 'rebill_period' ) : null;
    $product->rebill_access_duration = $product->is_recurring ? nullsafe_get( $data, 'rebill_period' ) + self::FAILED_PAYMENT_RETRY_PERIOD : null;

    return $product;
  }

}