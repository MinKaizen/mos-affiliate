<?php
/**
 * Plugin Name: MOS Affiliate
 * Description: Functions, shortcodes and APIs for MOS affiliate system. Currently relies on Indeed Ultimate Affiliate Pro
 */

namespace MOS\Affiliate;

// Check for wordpress environment
if ( !defined( '\ABSPATH' ) ) {
  die();
}

// Plugin constants
define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );
define( NS . 'PLUGIN_NAME', 'mos-affiliate' );
define( NS . 'PLUGIN_VERSION', '1.0.0' );
define( NS . 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( NS . 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

include( PLUGIN_DIR . '/includes/autoload.php' );

\register_activation_hook( __FILE__, NS.'Activator::activate' );

$mos_affiliate_plugin = new Plugin();
$mos_affiliate_plugin->init();