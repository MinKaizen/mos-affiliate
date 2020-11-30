<?php declare(strict_types=1);

namespace MOS\Affiliate\AccessRedirect;

use MOS\Affiliate\AccessRedirect;

class MonthlyPartnerAccessRedirect extends AccessRedirect {

  protected $tag='access_monthly_partner';
  protected $redirect_url='/no-access-monthly-partner';
  protected $cap=\MOS\Affiliate\CAP_MONTHLY_PARTNER;

}