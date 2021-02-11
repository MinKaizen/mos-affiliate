<?php declare(strict_types=1);

namespace MOS\Affiliate\ACF;

use MOS\Affiliate\ACF;
use MOS\Affiliate\Product;

class UserAccessACF extends ACF {

  function args(): array {
    $products = Product::get_all();
    $fields_array = [];

    foreach ( $products as $product ) {
      if ( $product->is_recurring ) {
        $fields_array[] = [
          'key' => "field_$product->access_meta_key",
          'label' => $product->name,
          'name' => $product->access_meta_key,
          'type' => 'date_time_picker',
          'instructions' => "Date until which user will have access to <code>$product->name</code>.",
          'required' => 0,
          'display_format' => 'Y-m-d',
          'return_format' => 'Y-m-d',
        ];
      } else {
        $fields_array[] = [
          'key' => "field_$product->access_meta_key",
          'label' => $product->name,
          'name' => $product->access_meta_key,
          'type' => 'radio',
          'instructions' => "Whether this user has access to <code>$product->name</code>",
          'required' => 0,
          'layout' => 'horizontal',
          'choices' => [
              '9999-01-01' => 'Access',
              '1999-01-01' => 'No Access',
          ],
          'allow_null' => 1,
        ];
      }
    }

    $field_group = array(
      'key' => 'group_mos_user_access',
      'title' => 'User Access',
      'fields' => $fields_array,
      'location' => [
        [
          [
            'param' => 'user_form',
            'operator' => '==',
            'value' => 'edit',
          ],
        ],
      ],
      'menu_order' => 0,
      'position' => 'acf_after_title',
      'style' => 'default',
      'label_placement' => 'left',
      'instruction_placement' => 'field',
      'active' => true,
    );

    return $field_group;
  }

}