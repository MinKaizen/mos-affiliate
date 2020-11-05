<?php

namespace MOS\Affiliate;

use MOS\Affiliate\Mis;
use MOS\Affiliate\Ability;

class User extends \WP_User {

  
  public static function current(): self {
    $wpid = \get_current_user_id();
    return self::from_id( $wpid );
  }


  public static function from_id( int $wpid ): self {
    $new_user = new self( $wpid );
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


  public function get_wpid() {
    return $this->ID;
  }


  public function get_affid() {
    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliates';
    $query = "SELECT id FROM $table WHERE uid = {$this->ID}";
    $affid = $wpdb->get_var( $query );

    $affid = $affid ? (int) $affid : 0;

    return $affid;
  }


  public function get_username() {
    return $this->user_login;
  }


  public function get_name() {
    if ($this->first_name && $this->last_name) {
      $name = implode( ' ', [$this->first_name, $this->last_name] );
    } elseif ( $this->first_name ) {
      $name = $this->first_name;
    } else {
      $name = $this->display_name;
    }
    
    return $name;
  }


  public function get_last_name() {
    return $this->last_name;
  }


  public function get_first_name() {
    return $this->first_name;
  }


  public function get_email() {
    return $this->user_email;
  }


  public function get_mis( $slug ): string {
    $mis = Mis::get( $slug );
    $value = $mis->exists() ? $this->get( $mis->meta_key ) : '';
    return $value;
  }


  public function get_level() {
    $roles = $this->roles;
    $primary_role = first_non_empty_element( $roles );
    $level = ucwords( str_replace( '_', ' ', $primary_role ) );
    return $level;
  }


  public function has_ability( string $ability ): bool {
    return $this->has_cap( Ability::cap_name( $ability ) );
  }


  public function qualifies_for_mis( string $network ): bool {
    $mis = Mis::get( $network );
    $qualifies = $mis->exists() ? $this->has_ability( $mis->ability ) : false;
    return $qualifies;
  }

}