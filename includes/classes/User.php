<?php

namespace MOS\Affiliate;

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


  public function wpid() {
    return $this->ID;
  }


  public function affid() {
    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliates';
    $query = "SELECT id FROM $table WHERE uid = {$this->ID}";
    $affid = $wpdb->get_var( $query );

    $affid = $affid ? (int) $affid : 0;

    return $affid;
  }


  public function username() {
    return $this->user_login;
  }


  public function name() {
    if ($this->first_name && $this->last_name) {
      $name = implode( ' ', [$this->first_name, $this->last_name] );
    } elseif ( $this->first_name ) {
      $name = $this->first_name;
    } else {
      $name = $this->display_name;
    }
    
    return $name;
  }


  public function last_name() {
    return $this->last_name;
  }


  public function first_name() {
    return $this->first_name;
  }


  public function email() {
    return $this->user_email;
  }


  public function mis( $slug ) {
    $prefix = 'mis_';
    $meta_key = $prefix . $slug;
    $mis = $this->get( $meta_key );
    return $mis;
  }


  public function level() {
    $roles = $this->roles;
    $primary_role = first_non_empty_element( $roles );
    $level = ucwords( str_replace( '_', ' ', $primary_role ) );
    return $level;
  }


  // NOTE TO SELF: uses alot of magic values. Needs refactoring
  public function qualifies_for_mis( string $network ): bool {
    if ( $network == 'cb_banners' ) {
      $purchased_oto2 = $this->get('mos_purchased_oto2');
      $qualified = !empty( $purchased_oto2 );
    } else {
      $level = $this->level();
      $levels_qualified = [
        'monthly_partner',
        'yearly_partner',
        'legacy_legendary_partner',
      ];
      $qualified = in_array( $level, $levels_qualified );
    }
    return $qualified;
  }

}