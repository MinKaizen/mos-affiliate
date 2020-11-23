<?php declare(strict_types=1);

namespace MOS\Affiliate\Level;

use \MOS\Affiliate\Level;

class FreeLevel extends Level {
  
  protected $name = 'Free Member';
  protected $slug = 'free';
  protected $order = 1;
  protected $caps = [
    \MOS\Affiliate\CAP_FREE,
  ];

}