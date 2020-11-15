<?php

namespace MOS\Affiliate;

define( NS . 'LEVELS', [
  'free' => [
    'name' => 'Free Member',
    'slug' => 'free',
    'order' => 1,
    'abilities' => [
      'access_free',
    ],
  ],
  'monthly_partner' => [
    'name' => 'Monthly Partner',
    'slug' => 'monthly_partner',
    'order' => 2,
    'abilities' => [
      'access_monthly_partner',
      'mis',
    ],
  ],
  'lifetime_partner' => [
    'name' => 'Lifetime Partner',
    'slug' => 'lifetime_partner',
    'order' => 3,
    'abilities' => [
      'access_lifetime_partner',
    ],
  ],
  'legacy_partner' => [
    'name' => 'Legacy Partner',
    'slug' => 'legacy_partner',
    'order' => 4,
    'abilities' => [
      'access_legacy_partner',
    ],
  ],
  'administrator' => [
    'name' => 'Administrator',
    'slug' => 'administrator',
    'order' => 99,
    'abilities' => [],
  ],
]);