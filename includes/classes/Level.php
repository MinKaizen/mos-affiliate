<?php

namespace MOS\Affiliate;

class Level {

  private $name = '';
  private $slug = '';
  private $order = 0;
  private $abilities = [];


  public static function get( string $slug ): self {
    $new_level = new Level();

    if ( ! in_array( $slug, array_keys( LEVELS ) ) ) {
      return $new_level;
    }

    $new_level->name = LEVELS[$slug]['name'];
    $new_level->slug = LEVELS[$slug]['slug'];
    $new_level->order = LEVELS[$slug]['order'];

    foreach ( LEVELS as $level ) {
      if ( $level['order'] <= $new_level->order ) {
        $new_level->abilities = array_merge( $new_level->abilities, $level['abilities'] );
      }
    }

    return $new_level;
  }


  public static function register_all_levels() {
    foreach ( LEVELS as $level ) {
      $new_level = self::get( $level['slug'] );
      $new_level->register();
    }
  }


  public static function slug_to_name( string $slug ): string {
    if ( in_array( $slug, array_keys( LEVELS ) ) ) {
      return LEVELS[$slug]['name'];
    } else {
      return ucwords( trim( preg_replace( '/[-_]+/', ' ', $slug ) ) );
    }
  }


  public function register(): void {
    if ( ! $this->exists() ) {
      return;
    }

    $capabilities = [];

    foreach ( $this->abilities as $ability ) {
      $capabilities[$ability] = true;
    }
    
    $new_role_success = \add_role( $this->slug, $this->name, $capabilities );

    if ( ! empty( $new_role_success ) ) {
      return;
    }

    $role = \get_role( $this->slug );

    foreach ( $capabilities as $capability => $grant ) {
      $role->add_cap( $capability, $grant );
    }
  }


  public function exists() {
    $exists = !empty( $this->slug );
    return $exists;
  }

}