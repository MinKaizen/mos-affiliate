<?php declare(strict_types=1);

namespace MOS\Affiliate\Shortcode;

use MOS\Affiliate\AbstractShortcode;
use MOS\Affiliate\User;

class CbVendorVariablesShortcode extends AbstractShortcode {

  protected $slug = 'mos_cb_vendor_variables';

  public function shortcode_action( $args ): string {
    $user = User::current();
    $sponsor = $user->sponsor();

    $params = [];
    $params[] = 'customer_wpid=' . $user->get_wpid();
    $params[] = 'customer_username=' . $user->get_username();
    $params[] = 'customer_name=' . $user->get_name();
    $params[] = 'customer_email=' . $user->get_email();
    $params[] = 'campaign=' . $user->get_campaign();
    $params[] = 'sponsor_wpid=' . $sponsor->get_wpid();
    $params[] = 'sponsor_username=' . $sponsor->get_username();
    $params[] = 'sponsor_name=' . $sponsor->get_name();
    $params[] = 'sponsor_email=' . $sponsor->get_email();

    $params_joined = implode( '&', $params );
    
    return $params_joined;
  }

}