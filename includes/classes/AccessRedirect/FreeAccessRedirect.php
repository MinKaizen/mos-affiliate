<?php

namespace MOS\Affiliate\AccessRedirect;

use MOS\Affiliate\AccessRedirect;

class FreeAccessRedirect extends AccessRedirect {

  protected $tag='access_free';
  protected $redirect_url='/';
  protected $cap=\MOS\Affiliate\CAP_FREE;

}