<?php declare(strict_types=1);

namespace MOS\Affiliate;

use stdClass;

function get_view( string $view_name, array $args=[] ): string {
  ob_start();
  render_view( $view_name, $args );
  $view = ob_get_clean();
  return $view;
}


function render_view( string $view_name, array $args=[] ): void {
  $view_file_name = PLUGIN_DIR . "/includes/views/$view_name.php";
  if ( !file_exists( $view_file_name ) ) {
    return;
  }
  
  $controller = Controller::get_controller( $view_name );
  extract( $controller->get_vars() );

  // If args passed, extract (and override controller args)
  if ( !empty( $args ) ) {
    extract( $args );
  }

  include( $view_file_name );
}


/**
 * Get classname from stub
 *
 * @param string $stub                The stub of the classname, in snake case
 * @param string $sub_dir (optional)  Optionally, the subdirectory where the class is located
 * @return string                     The fully qualified class name
 */
function class_name( string $stub, string $sub_dir='' ): string {
  $pascalized = snake_to_pascal_case( $stub );
  
  // Convert forward slashes to backwards slashes
  if ( ! empty( $sub_dir ) ) {
    $sub_dir = str_replace( "/", "\\", $sub_dir );
    $ends_in_slash = substr( $sub_dir, -1 ) === '\\';
    $sub_dir .= $ends_in_slash ? '' : '\\';
  }

  return NS . $sub_dir . $pascalized;
}


/**
 * Convert snake_case to PascalCase
 *
 * @param string $name            Name in snake case: abc_defg_hij
 * @return $camel_case            Name in camel case: AbcDefgHij
 */
function snake_to_pascal_case( string $name ): string {
  $pascal = str_replace( ' ', '', ucwords( implode( " ", explode( "_", $name ) ) ) );
  return $pascal;
}

/**
 * Convert PascalCase to snake_case
 *
 * @param string $snake_case
 * @return void
 */
function pascal_to_snake_case( string $snake_case ): string {
  preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $snake_case, $matches);
  $ret = $matches[0];
  foreach ($ret as &$match) {
    $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
  }
  return implode('_', $ret);
}


/**
 * Returns the first non-empty element of an array
 *
 * @param array $array      The array to be looped
 * @return mixed $element   The non-empty element       
 */
function first_non_empty_element( array $array ) {
  foreach ( $array as $element ) {
    if ( !empty( $element ) ) {
      return $element;
    }
  }
  return null;
}


/**
 * Whether a string is in the date form yyyy-mm-dd
 *
 * @param string $str   String to check
 * @return boolean
 */
function is_dateable( string $str ): bool {
  $dateable = true;

  $parts = explode( '-', $str );
  if ( count( $parts ) !== 3 ) {
    $dateable = false;
    return $dateable;
  }

  $year = $parts[0];
  $month = $parts[1];
  $day = $parts[2];

  $date = \DateTime::createFromFormat( 'Y-m-d', $str );
  if ( ! $date instanceof \DateTime ) {
    $dateable = false;
    return $dateable;
  }

  $dateable = ( $year == $date->format( 'Y' ) ) ? $dateable : false;
  $dateable = ( $month == $date->format( 'm' ) ) ? $dateable : false;
  $dateable = ( $day == $date->format( 'd' ) ) ? $dateable : false;

  return $dateable;
}


/**
 * Convert Proper case to kebab case
 *
 * @param string $proper_case     String in proper case (Hello World)
 * @return string                 String in kebab case (hello-world)
 */
function proper_to_kebab_case( string $proper_case ): string {
  $lower_case = strtolower( $proper_case );
  $kebab_case = str_replace( ' ', '-', $lower_case );
  return $kebab_case;
}


/**
 * Convert Proper case to kebab case
 *
 * @param string $snake_case     Snake case (hello_world)
 * @return string                Proper case (Hello World)
 */
function snake_to_proper_case( string $snake_case ): string {
  $words_separated = str_replace( '_', ' ', $snake_case );
  $proper_case = ucwords( $words_separated );
  return $proper_case;
}


function ranstr( int $length=32 ): string {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $characters_length = strlen($characters);
  $random_string = '';
  for ($i = 0; $i < $length; $i++) {
      $random_string .= $characters[rand(0, $characters_length - 1)];
  }
  return $random_string;
}


function format_currency( float $number, int $decimals=2 ): string {
  $sign = $number >= 0 ? '' : '-';
  $symbol = '$';
  $currency = $sign . $symbol . number_format( abs( $number ), $decimals );
  return $currency;
}


function expand_merge_tags( array $array, array $merge_tags ): array {
  $callback = function( &$value ) use ($merge_tags) {
    foreach ( $merge_tags as $search => $replace ) {
      if ( is_string( $value ) ) {
        $value = str_replace( $search, $replace, $value );
      }
    }
  };

  $new_array = $array;
  array_walk_recursive( $new_array, $callback );
  return $new_array;
}


function url_path_is( string $uri ): bool {
  if ( !isset( $_SERVER['REQUEST_URI'] ) ) {
    return false;
  }  

  $current_uri = trim( $_SERVER['REQUEST_URI'], '/' );
  $comparison_uri = trim( $uri, '/' );
  return $current_uri == $comparison_uri;
}


function acf_get_option( string $option_name, $fallback=null ) {
  // #NOTE: fallback is only for when ACF is not activated
  // Does not apply fallback if value is missing
  // or if option name is not found
  if ( function_exists( 'get_field' ) ) {
    $value = \get_field( $option_name, 'option' );
  } else {
    $value = $fallback;
  }
  return $value;
}


function mis_metakey_prefix(): string {
  $prefix = (string) acf_get_option( 'mis_metakey_prefix', 'mos_mis_' );
  return $prefix;
}


function mis_link_template_tag(): string {
  $prefix = (string) acf_get_option( 'mis_link_template_tag', '%affid%' );
  return $prefix;
}


function mis_value( string $slug, int $user_id=0 ): string {
  if ( !$user_id ) {
    $user_id = \apply_filters( 'mos_current_user_id', get_current_user_id() );
  }

  if ( !$user_id ) {
    return '';
  }

  $prefix = mis_metakey_prefix();

  $meta_key = $prefix . $slug;
  $mis = \get_user_meta( $user_id, $meta_key, true );
  return $mis;
}


function mis_object( string $slug ): object {
  $mis_query = new \WP_Query( [
    'post_type' => 'mis',
    'posts_per_page' => 1,
    'page' => 1,
    'meta_query' => [
      [
        'key' => 'slug',
        'value' => $slug,
      ]
    ],
  ] );

  if ( !isset( $mis_query->posts[0]->ID ) || !$mis_query->posts[0]->ID ) {
    return empty_mis_object();
  }

  $mis_object = mis_object_from_id( $mis_query->posts[0]->ID );

  return $mis_object;
}


function empty_mis_object(): object {
  $empty_mis_object = (object) [
    'exists' => false,
    'access_level' => '',
    'course_link' => '',
    'default' => '',
    'link_template' => '',
    'name' => '',
    'slug' => '',
    'meta_key' => '',
  ];

  return $empty_mis_object;
}


function mis_get_all(): array {
  $mis_query = new \WP_Query( [
    'post_type' => 'mis',
    'posts_per_page' => -1,
  ] );
  
  if ( !isset( $mis_query->posts ) || !is_array( $mis_query->posts ) ) {
    return [];
  }

  $all_mis = [];

  foreach ( $mis_query->posts as $post ) {
    $all_mis[] = mis_object_from_id( $post->ID );
  }

  return $all_mis;
}


function mis_object_from_id( int $id ): object {
  if ( !function_exists( 'get_field' ) ) {
    return empty_mis_object();
  }
  
  $mis_object = new stdClass();

  $mis_object->exists = post_is_mis( $id );
  $mis_object->access_level = get_field( 'access_level', $id );
  $mis_object->course_link = get_field( 'course_link', $id );
  $mis_object->default = get_field( 'default', $id );
  $mis_object->link_template = get_field( 'link_template', $id );
  $mis_object->name = get_field( 'name', $id );
  $mis_object->slug = get_field( 'slug', $id );
  $mis_object->meta_key = mis_metakey_prefix() . get_field( 'slug', $id );

  return $mis_object;
}


function post_is_mis( int $post_id ): bool {
  $post = \get_post( $post_id );
  $is_mis = ($post instanceof \WP_Post) && ($post->post_type == 'mis');
  return $is_mis;
}


function mis_generate_link( string $slug, string $value ): string {
  $mis_object = mis_object( $slug );

  if ( empty($mis_object->link_template) || !$mis_object->link_template ) {
    return '';
  }

  $link = str_replace( mis_link_template_tag(), $value, $mis_object->link_template );
  return $link;
}


function mis_default_value( string $slug ): string {
  $mis_object = mis_object( $slug );
  return $mis_object->default;
}