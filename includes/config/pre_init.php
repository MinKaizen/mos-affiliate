<?php declare(strict_types=1);

namespace MOS\Affiliate;

define( 'PHP_VERSION_MIN', '7.1.0' );

define( 'FUNCTION_DEPENDENCIES', [
  'get_field',
] );

define( 'CLASS_DEPENDENCIES', [
  'UAP_Main',
  'MGC\REST_Route\REST_Route',
  'MGC\Logger\Logger',
] );