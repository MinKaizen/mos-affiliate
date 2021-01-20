<?php declare(strict_types=1);

namespace MOS\Affiliate;

use \Error;

class Level {

  public $slug;
  public $exists = false;
  public $granted_by = [];
  public $grants = [];

  const CONFIG = PLUGIN_DIR . '/includes/config/levels.json';

  public function __construct( string $slug ) {
    if ( !file_exists( self::CONFIG ) ) {
      $error_msg = "Could not find levels config at: " . self::CONFIG;
      echo $error_msg;
      throw new Error( $error_msg );
    }

    $levels = json_decode( file_get_contents( self::CONFIG ) );
    $level_index = array_search( $slug, $levels );

    if ( $level_index === false ) {
      return;
    } 

    $this->slug = $slug;
    $this->exists = true;
    $this->granted_by = array_slice( $levels, $level_index );
    $this->grants = array_slice( $levels, 0, $level_index+1 );
  }

}