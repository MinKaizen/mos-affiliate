<?php

namespace MOS\Affiliate;

define( NS . 'MIS_SLUG_MAX_LEN', 20 );
define( NS . 'MIS_LINK_PLACEHOLDER', '' . '%affid%' );
define( NS . 'MIS_META_KEY_PREFIX', 'mos_mis_' );

define( NS . 'MIS_NETWORKS', [
  'gr' => [
    'meta_key' => 'gr',
    'name' => 'Get Response',
    'default' => 'htlcb',
    'link_template' => 'https://www.google.com/search?q=' . MIS_LINK_PLACEHOLDER,
    'ability' => 'mis',
  ],
  'cb' => [
    'meta_key' => 'cb',
    'name' => 'Clickbank',
    'default' => 'htlcb',
    'link_template' => 'https://www.google.com/search?q=' . MIS_LINK_PLACEHOLDER,
    'ability' => 'mis',
  ],
  'cm' => [
    'meta_key' => 'cm',
    'name' => 'Click Magick',
    'default' => 'htlcb',
    'link_template' => 'https://www.google.com/search?q=' . MIS_LINK_PLACEHOLDER,
    'ability' => 'mis',
  ],
  'cb_banners' => [
    'meta_key' => 'cb',
    'name' => 'Clickbank (Banners)',
    'default' => 'htlcb',
    'link_template' => 'https://www.google.com/search?q=' . MIS_LINK_PLACEHOLDER,
    'ability' => 'banner-mis',
  ],
] );