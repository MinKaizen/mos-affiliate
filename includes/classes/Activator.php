<?php

namespace MOS\Affiliate;

class Activator {

  public static function activate() {
    Upgrader::upgrade();
    Level::register_all_levels();
  }

}