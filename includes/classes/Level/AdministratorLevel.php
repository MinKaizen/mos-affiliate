<?php declare(strict_types=1);

namespace MOS\Affiliate\Level;

use \MOS\Affiliate\Level;

class AdministratorLevel extends Level {
  
  protected $name = 'Administrator';
  protected $slug = 'administrator';
  protected $order = 99;
  protected $caps = [];

}