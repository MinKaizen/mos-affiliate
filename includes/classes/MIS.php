<?php declare(strict_types=1);

namespace MOS\Affiliate;

class MIS {

  const LINK_PLACEHOLDER = '%affid%';
  const POST_TYPE = 'mis';

  public $exists = false;
  public $access_level = '';
  public $course_link = '';
  public $default = '';
  public $link_template = '';
  public $meta_key = '';
  public $name = '';
  public $slug = '';

  public function __construct( string $slug ) {
    if ( !function_exists( 'get_field' ) ) {
      return;
    }

    $mis_query = self::mis_query( $slug );

    if ( !$mis_query->have_posts() ) {
      return;
    } else {
      $mis_query->the_post();
    }

    $this->exists = true;
    $this->access_level = get_field( 'access_level' );
    $this->course_link = get_field( 'course_link' );
    $this->default = get_field( 'default' );
    $this->link_template = get_field( 'link_template' );
    $this->meta_key = get_field( 'meta_key' );
    $this->name = get_field( 'name' );
    $this->slug = get_field( 'slug' );
  }

  private static function mis_query( string $slug ) {
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

    return $mis_query;
  }

  public static function default_value_for( string $slug ): string {
    $new_mis = new self( $slug );
    return $new_mis->default;
  }

  public static function get_all() {
    $mis_query = new \WP_Query( [
      'post_type' => 'mis',
      'posts_per_page' => -1,
    ] );

    $all_mis = [];

    foreach ( $mis_query->get_posts() as $post ) {
      $slug = get_field( 'slug', $post->ID );
      $all_mis[] = new self( $slug );
    }

    return $all_mis;
  }

  public function generate_link( string $mis_value ): string {
    $search = self::LINK_PLACEHOLDER;
    $replace = $mis_value;
    $subject = $this->link_template;
    $link = str_replace( $search, $replace, $subject );
    return $link;
  }

}