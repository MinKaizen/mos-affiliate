<?php declare(strict_types=1);

namespace MOS\Affiliate;

add_action('init', function() {
  /**
   * Post Type: MIS.
   */

  $labels = [
    "name" => __("MIS", "sage"),
    "singular_name" => __("MIS", "sage"),
    "menu_name" => __("MIS", "sage"),
    "all_items" => __("All MIS", "sage"),
    "add_new" => __("Add new", "sage"),
    "add_new_item" => __("Add new MIS", "sage"),
    "edit_item" => __("Edit MIS", "sage"),
    "new_item" => __("New MIS", "sage"),
    "view_item" => __("View MIS", "sage"),
    "view_items" => __("View MIS", "sage"),
    "search_items" => __("Search MIS", "sage"),
    "not_found" => __("No MIS found", "sage"),
    "not_found_in_trash" => __("No MIS found in trash", "sage"),
    "parent" => __("Parent MIS:", "sage"),
    "featured_image" => __("Featured image for this MIS", "sage"),
    "set_featured_image" => __("Set featured image for this MIS", "sage"),
    "remove_featured_image" => __("Remove featured image for this MIS", "sage"),
    "use_featured_image" => __("Use as featured image for this MIS", "sage"),
    "archives" => __("MIS archives", "sage"),
    "insert_into_item" => __("Insert into MIS", "sage"),
    "uploaded_to_this_item" => __("Upload to this MIS", "sage"),
    "filter_items_list" => __("Filter MIS list", "sage"),
    "items_list_navigation" => __("MIS list navigation", "sage"),
    "items_list" => __("MIS list", "sage"),
    "attributes" => __("MIS attributes", "sage"),
    "name_admin_bar" => __("MIS", "sage"),
    "item_published" => __("MIS published", "sage"),
    "item_published_privately" => __("MIS published privately.", "sage"),
    "item_reverted_to_draft" => __("MIS reverted to draft.", "sage"),
    "item_scheduled" => __("MIS scheduled", "sage"),
    "item_updated" => __("MIS updated.", "sage"),
    "parent_item_colon" => __("Parent MIS:", "sage"),
  ];

  $args = [
    "label" => __("MIS", "sage"),
    "labels" => $labels,
    "description" => "",
    "public" => true,
    "publicly_queryable" => true,
    "show_ui" => true,
    "show_in_rest" => true,
    "rest_base" => "mis",
    "rest_controller_class" => "WP_REST_Posts_Controller",
    "has_archive" => false,
    "show_in_menu" => true,
    "show_in_nav_menus" => false,
    "delete_with_user" => false,
    "exclude_from_search" => true,
    "capability_type" => "post",
    "map_meta_cap" => true,
    "hierarchical" => false,
    "rewrite" => ["slug" => "mis", "with_front" => true],
    "query_var" => true,
    "menu_position" => 5,
    "menu_icon" => "dashicons-rest-api",
    "supports" => ["title", "custom-fields"],
  ];

  register_post_type( "mis", $args );
} );