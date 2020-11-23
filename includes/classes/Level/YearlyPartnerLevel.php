<?php declare(strict_types=1);

namespace MOS\Affiliate\Level;

use \MOS\Affiliate\Level;

class YearlyPartnerLevel extends Level {
  
  protected $name = 'Yearly Partner';
  protected $slug = 'yearly_partner';
  protected $order = 3;
  protected $caps = [
    \MOS\Affiliate\CAP_YEARLY_PARTNER,
  ];

}