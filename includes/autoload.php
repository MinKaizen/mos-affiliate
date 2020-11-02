<?php

namespace MOS\Affiliate;

spl_autoload_register( function( $class_name ) {
  if ( strpos( $class_name, __NAMESPACE__ ) === false ) {
    return;
  }

  $remove_prefix = str_replace( __NAMESPACE__, '', $class_name );
  $convert_slashes = str_replace( '\\', '/', $remove_prefix );
  $file_name = __DIR__ . '/classes' . $convert_slashes . ".php";
  require_once( $file_name );
} );