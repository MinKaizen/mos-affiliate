<?php declare(strict_types=1);

namespace MOS\Affiliate\Mis;

use \MOS\Affiliate\Mis;

class GetResponseMis extends Mis {

  protected $meta_key = 'gr';
  protected $name = 'Get Response';
  protected $slug = 'gr';
  protected $default = 'htlcb';
  protected $link_template = 'https://www.google.com/search?q=' . parent::MIS_LINK_PLACEHOLDER;
  protected $cap = \MOS\Affiliate\CAP_MIS;

}