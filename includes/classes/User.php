<?php

namespace MOS\Affiliate;

use MOS\Affiliate\Mis;

class User extends \WP_User {

  
  public static function current(): self {
    $wpid = \get_current_user_id();
    $wpid = \apply_filters( 'mos_current_user_id', $wpid );
    return self::from_id( $wpid );
  }


  public static function from_id( int $wpid ): self {
    $new_user = new self( $wpid );
    $new_user = \apply_filters( 'mos_current_user', $new_user, $wpid );
    return $new_user;
  }


  public static function from_affid( int $affid ): self {
    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliates';
    $query = "SELECT uid FROM $table WHERE id = $affid";
    $wpid = $wpdb->get_var( $query );
    $wpid = $wpid ? $wpid : 0;

    return self::from_id( $wpid );
  }


  public static function from_username( string $username ): self {
    global $wpdb;

    $table = $wpdb->prefix . 'users';
    $query = "SELECT id FROM $table WHERE user_login = '$username'";
    $wpid = $wpdb->get_var( $query );
    $wpid = $wpid ? $wpid : 0;

    return self::from_id( $wpid );
  }


  public function sponsor(): self {
    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
    $query = "SELECT affiliate_id FROM $table WHERE referral_wp_uid = $this->ID";
    $sponsor_affid = $wpdb->get_var( $query );
    $sponsor_affid = $sponsor_affid ? $sponsor_affid : 0;

    return self::from_affid( $sponsor_affid );
  }


  public function is_empty(): bool {
    $is_empty = empty( $this->ID );
    return $is_empty;
  }


  public function get_wpid(): int {
    $wpid = $this->ID ? $this->ID : 0;
    return $wpid;
  }


  public function get_affid():int {
    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliates';
    $query = "SELECT id FROM $table WHERE uid = {$this->ID}";
    $affid = $wpdb->get_var( $query );

    $affid = $affid ? (int) $affid : 0;

    return $affid;
  }


  public function get_username(): string {
    $username = $this->user_login ? $this->user_login : '';
    return $username;
  }


  public function get_name(): string {
    if ($this->first_name && $this->last_name) {
      $name = implode( ' ', [$this->first_name, $this->last_name] );
    } elseif ( $this->first_name ) {
      $name = $this->first_name;
    } elseif ( $this->display_name ) {
      $name = $this->display_name;
    } else {
      $name = '';
    }
    
    return $name;
  }


  public function get_last_name(): string {
    $last_name = $this->last_name ? $this->last_name : '';
    return $last_name;
  }


  public function get_first_name(): string {
    $first_name = $this->first_name ? $this->first_name : '';
    return $first_name;
  }


  public function get_email(): string {
    $email = $this->user_email ? $this->user_email : '';
    return $email;
  }


  public function get_mis( $slug ): string {
    $mis = Mis::get( $slug );
    $value = $mis->exists() ? $this->get( $mis->meta_key ) : '';
    return $value;
  }


  public function get_level(): string {
    $roles = (array) $this->roles;
    $primary_role = empty( $roles ) ? '' : first_non_empty_element( $roles );
    $level = Level::slug_to_name( $primary_role );
    return $level;
  }


  public function qualifies_for_mis( string $network ): bool {
    $mis = Mis::get( $network );
    $qualifies = $mis->exists() ? $this->has_cap( $mis->cap ) : false;
    return $qualifies;
  }

}