<?php declare(strict_types=1);

namespace MOS\Affiliate\Level;

use \MOS\Affiliate\Level;

class MonthlyPartnerLevel extends Level {
  
  protected $name = 'Monthly Partner';
  protected $slug = 'monthly_partner';
  protected $order = 2;
  protected $caps = [
    \MOS\Affiliate\CAP_MONTHLY_PARTNER,
    \MOS\Affiliate\CAP_MIS,
  ];

}