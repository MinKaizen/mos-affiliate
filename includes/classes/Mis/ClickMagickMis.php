<?php declare(strict_types=1);

namespace MOS\Affiliate\Mis;

use \MOS\Affiliate\Mis;

class ClickMagickMis extends Mis {

  protected $meta_key = parent::MIS_META_KEY_PREFIX . 'cm';
  protected $name = 'Click Magick';
  protected $slug = 'cm';
  protected $default = 'htlcb';
  protected $link_template = 'https://www.google.com/search?q=' . parent::MIS_LINK_PLACEHOLDER;
  protected $cap = \MOS\Affiliate\CAP_MIS;

}