<?php

namespace MOS\Affiliate;

define( NS . 'LEVELS', [
  'free' => [
    'name' => 'Free Member',
    'slug' => 'free',
    'order' => 1,
    'caps' => [
      CAP_FREE,
    ],
  ],
  'monthly_partner' => [
    'name' => 'Monthly Partner',
    'slug' => 'monthly_partner',
    'order' => 2,
    'caps' => [
      CAP_MONTHLY_PARTNER,
      CAP_MIS,
    ],
  ],
  'yearly_partner' => [
    'name' => 'Yearly Partner',
    'slug' => 'yearly_partner',
    'order' => 3,
    'caps' => [
      CAP_YEARLY_PARTNER,
    ],
  ],
  'administrator' => [
    'name' => 'Administrator',
    'slug' => 'administrator',
    'order' => 99,
    'caps' => [],
  ],
]);