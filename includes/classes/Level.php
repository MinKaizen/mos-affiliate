<?php

namespace MOS\Affiliate;

class Level {

  const LEVELS = [
    'free',
    'monthly_partner',
    'yearly_partner',
  ];

  protected $name = '';
  protected $slug = '';
  protected $order = 0;
  protected $caps = [];


  public static function get( string $slug ): self {
    $class_name = class_name( $slug . "_level", 'Level' );
    if ( class_exists( $class_name ) ) {
      return new $class_name();
    } else {
      return new self();
    }
  }


  public static function sort( self $level1, self $level2 ): int {
    return $level1->get_order() - $level2->get_order();
  }


  public static function register_all_levels() {
    foreach ( self::LEVELS as $level ) {
      $levels[] = Level::get( $level );
    }
    
    // Higher order levels inherit caps
    usort( $levels, ['MOS\Affiliate\Level', 'sort'] );
    $previous_caps = [];
    foreach ( $levels as $level ) {
      if ( !empty( $previous_caps ) ) {
        $level->add_caps( $previous_caps );
      }
      $previous_caps = $level->get_caps();
    }

    // Register each level
    foreach ( $levels as $level ) {
      $level->register();
    }
  }


  public static function slug_to_name( string $slug ): string {
    $level = self::get( $slug );

    if ( $level->exists() ) {
      return $level->get_name();
    } else {
      return ucwords( trim( preg_replace( '/[-_]+/', ' ', $slug ) ) );
    }
  }


  public function exists() {
    $exists = !empty( $this->slug );
    return $exists;
  }


  public function get_name(): string {
    return $this->name;
  }


  public function get_slug(): string {
    return $this->slug;
  }


  public function get_order(): int {
    return $this->order;
  }


  public function get_caps(): array {
    return $this->caps;
  }


  public function add_caps( array $extra_caps ): void {
    foreach( $extra_caps as $extra_cap ) {
      $this->caps[] = $extra_cap;
    }
  }


  public function is_greater_than( self $other_level ): bool {
    return ( $this->order > $other_level->order );
  }


  public function is_less_than( self $other_level ): bool {
    return ( $this->order < $other_level->order );
  }


  public function is_atleast( self $other_level ): bool {
    return ( $this->order >= $other_level->order );
  }


  public function has_cap( string $cap ): bool {
    return ( in_array( $cap, $this->caps ) );
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


}