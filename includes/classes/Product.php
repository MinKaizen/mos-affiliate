<?php declare(strict_types=1);

namespace MOS\Affiliate;

use \Error;

class Product {

  const CONFIG = PLUGIN_DIR . '/includes/config/products.json';
  const FAILED_PAYMENT_RETRY_PERIOD = 14;
  # See this https://support.clickbank.com/hc/en-us/articles/220364187-Selling-Recurring-Products#h_e150423c-bf1b-456b-931d-cd7dd852633f
  
  public $cb_id;
	public $slug;
  public $name;
  public $access_meta_key;
  
  # price is also the initial price for recurring products
  public $price; 

	public $trial_price;
  public $trial_period;
	public $trial_access_duration;
  
  public $rebill_price;
	public $rebill_period;
  public $rebill_access_duration;
  
	public $has_trial_period;
	public $is_recurring;


	public function __construct( string $product_slug ) {
    $products = json_decode( file_get_contents( self::CONFIG ) );
    $product = nullsafe_get( $products, $product_slug );

    if ( !$product ) {
      $error_msg = "Could not construct Product from slug: $product_slug";
      echo $error_msg;
      throw new Error( $error_msg );
    }

		$this->slug = $product_slug;
		$this->cb_id = nullsafe_get( $product, 'cb_id' );
    $this->name = nullsafe_get( $product, 'name' );
    $this->access_meta_key = nullsafe_get( $product, 'access_meta_key' );

    $this->price = nullsafe_get( $product, 'price' );

    $this->has_trial_period = $this->trial_period ? true : false;
    $this->trial_price = $this->has_trial_period ? nullsafe_get( $product, 'price' ) : null;
    $this->trial_period = nullsafe_get( $product, 'trial_period' );
    $this->trial_access_duration = nullsafe_get( $product, 'trial_period' ) + self::FAILED_PAYMENT_RETRY_PERIOD;;
    
    $this->is_recurring = $this->rebill_price && $this->rebill_period ? true : false;
    $this->rebill_price = nullsafe_get( $product, 'rebill_price' );
    $this->rebill_period = nullsafe_get( $product, 'rebill_period' );
    $this->rebill_access_duration = nullsafe_get( $product, 'rebill_period' ) + self::FAILED_PAYMENT_RETRY_PERIOD;
  }


	public static function from_cb_id( int $cb_id ): self {
		$products = json_decode( file_get_contents( self::CONFIG ) );
    
    foreach ( $products as $slug => $product ) {
      if ( nullsafe_get( $product, 'cb_id' ) === $cb_id ) {
        return new self( $slug );
      }
    }

    $error_msg = "Could not construct Product from cb_id: $cb_id";
    echo $error_msg;
    throw new Error( $error_msg );
	}

}