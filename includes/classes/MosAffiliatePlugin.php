<?php

class MosAffiliatePlugin {

  private $path = '';
  private $url = '';


  public function construct( string $plugin_file ) {
    $this->path = plugin_dir_path( $plugin_file );
    $this->url = plugin_dir_url( $plugin_file );
  }


  public function init() {
    $this->load_dependencies();
  }


  private function load_dependencies() {

  }

}