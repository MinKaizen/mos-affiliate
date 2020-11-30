<?php declare(strict_types=1);

namespace MOS\Affiliate;

class Activator {

  public static function activate(): void {
    Upgrader::upgrade();
    Level::register_all_levels();
  }

}