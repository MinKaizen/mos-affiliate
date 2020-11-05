<?php

namespace MOS\Affiliate;

class Ability {

  public static function cap_name( string $ability ): string {
    return ABILITY_CAP_PREFIX . $ability;
  }


  public static function exists( string $ability ): bool {
    return in_array( $ability, ABILITIES );
  }


  public static function list(): array {
    return ABILITIES;
  }

}