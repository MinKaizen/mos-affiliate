<?php

namespace MOS\Affiliate;

class Mis {

  protected $meta_key = '';
  protected $name = '';
  protected $default = '';
  protected $link_template = '';
  protected $cap = '';


  public static function get( string $slug ): self {
    $new_mis = new self;
    
    if ( ! in_array( $slug, array_keys(MIS_NETWORKS) )) {
      return $new_mis;
    }

    $new_mis->meta_key = MIS_META_KEY_PREFIX . MIS_NETWORKS[$slug]['meta_key'];
    $new_mis->name = MIS_NETWORKS[$slug]['name'];
    $new_mis->default = MIS_NETWORKS[$slug]['default'];
    $new_mis->link_template = MIS_NETWORKS[$slug]['link_template'];
    $new_mis->cap = MIS_NETWORKS[$slug]['cap'];

    return $new_mis;
  }


  public static function get_default( string $mis_slug ): string {
    $mis = self::get( $mis_slug );
    $default_value = $mis->exists() ? $mis->default : '';
    return $default_value;
  }


  public function exists(): bool {
    $exists = ! empty( $this->meta_key );
    return $exists;
  }


  public function get_meta_key(): string {
    return $this->meta_key;
  }


  public function get_name(): string {
    return $this->name;
  }


  public function get_link_template(): string {
    return $this->link_template;
  }


  // public function get_default(): string {
    
  // }


  public function get_cap(): string {
    return $this->cap;
  }


  public function generate_link( string $mis_value ): string {
    $link = str_replace( MIS_LINK_PLACEHOLDER, $mis_value, $this->link_template );
    return $link;
  }


}