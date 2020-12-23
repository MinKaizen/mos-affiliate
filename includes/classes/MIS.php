<?php declare(strict_types=1);

namespace MOS\Affiliate;

class MIS {

  const CONFIG = PLUGIN_DIR . 'includes/config/mis.json';
  const LINK_PLACEHOLDER = '%affid%';

  public $exists = false;
  public $name = '';
  public $slug = '';
  public $meta_key = '';
  public $default = '';
  public $link_template = '';
  public $access_level = '';

  public function __construct( string $slug ) {
    if ( !file_exists( self::CONFIG ) ) {
      throw new \Error('MIS config file could not be found at ' . self::CONFIG );
    }

    $mis_data = $this->load_data_from_json( self::CONFIG );

    if ( !isset( $mis_data[$slug] ) ) {
      return;
    }

    $this->exists = true;
    $this->name = $mis_data[$slug]['name'] ?? $this->name;
    $this->slug = $mis_data[$slug]['slug'] ?? $this->slug;
    $this->meta_key = $mis_data[$slug]['meta_key'] ?? $this->meta_key;
    $this->default = $mis_data[$slug]['default'] ?? $this->default;
    $this->link_template = $mis_data[$slug]['link_template'] ?? $this->link_template;
    $this->access_level = $mis_data[$slug]['access_level'] ?? $this->access_level;
  }

  public static function default_value_for( string $slug ): string {
    $new_mis = new self( $slug );
    return $new_mis->default;
  }

  public function generate_link( string $mis_value ): string {
    $search = self::LINK_PLACEHOLDER;
    $replace = $mis_value;
    $subject = $this->link_template;
    $link = str_replace( $search, $replace, $subject );
    return $link;
  }

  private function load_data_from_json( string $config_file_path ): array {
    $json = (string) \file_get_contents( $config_file_path );
    $data = (array) json_decode( $json, true );
    return $data;
  }

}