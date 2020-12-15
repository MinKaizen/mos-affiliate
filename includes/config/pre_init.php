<?php declare(strict_types=1);

namespace MOS\Affiliate;

define( 'PHP_VERSION_MIN', '7.1.0' );

define( 'FUNCTION_DEPENDENCIES', [
  '\MOS\Async\add_action_async',
] );

define( 'CLASS_DEPENDENCIES', [
  'UAP_Main',
  'MGC\REST\Route',
] );