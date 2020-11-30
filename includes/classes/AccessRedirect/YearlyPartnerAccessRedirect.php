<?php declare(strict_types=1);

namespace MOS\Affiliate\AccessRedirect;

use MOS\Affiliate\AccessRedirect;

class YearlyPartnerAccessRedirect extends AccessRedirect {

  protected $tag='access_yearly_partner';
  protected $redirect_url='/no-access-yearly-partner';
  protected $cap=\MOS\Affiliate\CAP_YEARLY_PARTNER;

}