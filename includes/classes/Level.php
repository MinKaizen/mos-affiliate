<?php

namespace MOS\Affiliate;

class Level {

  private $name = '';
  private $slug = '';
  private $order = 0;
  private $caps = [];


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
        $new_level->caps = array_merge( $new_level->caps, $level['caps'] );
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

    $caps_to_insert = [];

    foreach ( $this->caps as $cap ) {
      $caps_to_insert[$cap] = true;
    }
    
    $new_role_success = \add_role( $this->slug, $this->name, $caps_to_insert );

    if ( ! empty( $new_role_success ) ) {
      return;
    }

    $role = \get_role( $this->slug );

    foreach ( $caps_to_insert as $cap_to_insert => $grant ) {
      $role->add_cap( $cap_to_insert, $grant );
    }
  }


  public function exists() {
    $exists = !empty( $this->slug );
    return $exists;
  }

}