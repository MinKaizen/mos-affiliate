<?php declare(strict_types=1);

namespace MOS\Affiliate;

class Mis {

  const MIS_SLUG_MAX_LEN =  20;
  const MIS_LINK_PLACEHOLDER = '%affid%';
  const MIS_META_KEY_PREFIX = 'mos_mis_';

  protected $meta_key = '';
  protected $name = '';
  protected $slug = '';
  protected $default = '';
  protected $link_template = '';
  protected $cap = '';


  public static function get( string $slug ): self {
    $mis_class = class_name( $slug . '_mis', 'Mis' );
    if ( class_exists( $mis_class ) ) {
      return new $mis_class();
    } else {
      return new self();
    }
  }


  public static function default_value_for( string $mis_slug ): string {
    $mis = self::get( $mis_slug );
    $default_value = $mis->exists() ? $mis->get_default() : '';
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


  public function get_default(): string {
    return $this->default;
  }


  public function get_cap(): string {
    return $this->cap;
  }


  public function get_slug(): string {
    return $this->slug;
  }


  public function generate_link( string $mis_value ): string {
    $link = str_replace( self::MIS_LINK_PLACEHOLDER, $mis_value, $this->link_template );
    return $link;
  }


}