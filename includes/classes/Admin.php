<?php

namespace MOS\Affiliate;

class Admin {

  private $parent_menus = [
    [
      'view' => 'admin_mis_page',
      'title' => 'MOS Affiliate',
      'slug' => 'mos-affiliate',
      'icon' => 'dashicons-rest-api',
      'position' => 3,
    ],
  ];

  private $sub_menus = [
    // [
    //   'parent_slug' => 'mos-affiliate',
    //   'title' => 'Submenu1',
    //   'slug' => 'mos-affiliate-sub1',
    //   'view' => 'view_name',
    // ],
  ];


  public function init() {
    \add_action( 'admin_enqueue_scripts', [$this, 'enqueue_styles'] );
    \add_action( 'admin_menu', [$this, 'register_menus'] );
  }


  public function enqueue_styles( $hook_suffix ) {

  }


  public function register_menus() {
    foreach ( $this->parent_menus as $parent_menu ) {
      $this->add_parent_menu( $parent_menu );
    }

    foreach ( $this->sub_menus as $sub_menu ) {
      $this->add_sub_menu( $sub_menu );
    }

  }


  private function add_parent_menu( $args ) {
    $defaults = [
      'title' => '',
      'capability' => 'manage_options',
      'slug' => '',
      'icon' => '',
      'position' => null,
      'view' => '',
    ];

    $options = array_replace( $defaults, $args );

    \add_menu_page(
      $options['title'],
      $options['title'],
      $options['capability'],
      $options['slug'],
      function() use ($options) { render_view( $options['view'] ); },
      $options['icon'],
      $options['position'],
    );

  }


  private function add_sub_menu( $args ) {
    $defaults = [
      'parent_slug' => '',
      'title' => '',
      'capability' => 'manage_options',
      'slug' => '',
      'position' => null,
      'view' => '',
    ];

    $options = array_replace( $defaults, $args );

    \add_submenu_page(
      $options['parent_slug'],
      $options['title'],
      $options['title'],
      $options['capability'],
      $options['slug'],
      function() use ($options) { render_view( $options['view'] ); },
      $options['position'],
    );

  }


}