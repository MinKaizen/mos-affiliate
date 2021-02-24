<?php declare(strict_types=1);

namespace MOS\Affiliate\ActionHooks;

use \MOS\Affiliate\ActionHook;
use \MGC\Logger\Logger;
use \MOS\Affiliate\Access;
use \MOS\Affiliate\Product;

class GformGrantAccess extends ActionHook {

  protected $hook = 'gform_after_submission';
  protected $args = 2;
  private $logger;

  public function __construct() {
    $this->logger = new Logger( 'mos-affiliate', 'gform_grant_access.log' );
  }

  public function handler( $entry, $form ): void {
    $wpid = $this->get_wpid( $entry );
    if ( !$wpid ) {
      return;
    }

    $product = $this->get_product( $form );
    if ( !$product->exists ) {
      return;
    }
    
    $this->grant_access( $wpid, $product );
    $this->log( $entry, $form );
  }

  private function grant_access( int $wpid, Product $product ): void {
    Access::grant_access( $wpid, $product, 'SALE' );
  }

  private function log( $entry, $form ): void {
    $wpid = $this->get_wpid( $entry );
    $url = $this->get_url( $entry );
    $product_slug = $this->get_product_slug( $form );
    $payment = $this->get_payment( $entry );

    $message = json_encode( [
      'wpid' => $wpid,
      'url' => $url,
      'product_slug' => $product_slug,
      'payment' => $payment,
    ] );
    
    $this->logger->log( $message );
  }

  private function get_wpid( $entry ): int {
    $wpid = isset( $entry['created_by'] ) ? (int) $entry['created_by'] : 0;
    return $wpid;
  }

  private function get_url( $entry ): string {
    $url = isset( $entry['source_url'] ) ? (string) $entry['source_url'] : '';
    return $url;
  }

  private function get_product( $form ): Product {
    $product_slug = $this->get_product_slug( $form );
    $product = Product::from_slug( $product_slug);
    return $product;
  }

  private function get_product_slug( $form ): string {
    $product_slug = '';

    if ( !empty($form['fields'] ) ) {
      foreach ( $form['fields'] as $field ) {
        if ( $field->adminLabel == '_grant_access' ) {
          $product_slug = $field->defaultValue;
          break;
        }
      }
    }

    return $product_slug;
  }

  private function get_payment( $entry ): array {
    $payment = [
      'date' => isset( $entry['payment_date'] ) ? $entry['payment_date'] : '',
      'amount' => isset( $entry['payment_amount'] ) ? $entry['payment_amount'] : 0,
      'method' => isset( $entry['payment_method'] ) ? $entry['payment_method'] : '',
      'transaction_id' => isset( $entry['transaction_id'] ) ? $entry['transaction_id'] : '',
    ];
    return $payment;
  }

}