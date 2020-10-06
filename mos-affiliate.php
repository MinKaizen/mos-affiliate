<?php
/**
 * Plugin Name: MOS Affiliate
 * Description: Functions, shortcodes and APIs for MOS affiliate system. Currently relies on Indeed Ultimate Affiliate Pro
 */

// Check for wordpress environment
if ( !defined( 'ABSPATH' ) ) {
  die();
}

require_once( 'includes/classes/MosAffiliatePlugin.php' );

if ( class_exists( 'MosAffiliatePlugin' ) ) {
  $mos_affiliate_plugin = new MosAffiliatePlugin( __FILE__ );
  $mos_affiliate_plugin->init();
}