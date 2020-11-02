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
define( NS . 'PLUGIN_NAME', 'mos-birdsend' );
define( NS . 'PLUGIN_VERSION', '1.0.0' );
define( NS . 'PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( NS . 'PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Composer autoloader
if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
  require __DIR__ . '/vendor/autoload.php';
}

require_once( PLUGIN_DIR . '/includes/classes/MosAffiliatePlugin.php' );

if ( class_exists( NS.'MosAffiliatePlugin' ) ) {
  $mos_affiliate_plugin = new MosAffiliatePlugin();
  $mos_affiliate_plugin->init();
  add_action( 'admin_notices', function() {
    echo '<div class="notice notice-success"><p>';
    echo '<strong>mos-affiliate plugin:</strong> ';
    echo 'after plugin initiated';
    echo '</p></div>';
  });
}