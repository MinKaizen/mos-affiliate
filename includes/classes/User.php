<?php

namespace MOS\Affiliate;

class User extends \WP_User {

  
  public static function current() {
    $wpid = \get_current_user_id();
    $user = empty( $wpid ) ? false : new User( $wpid );
    $user = empty( $user->id ) ? false : $user;
    return $user;
  }


  public static function from_id( int $wpid ) {
    return \get_user_by( 'id', $wpid );
  }


  public static function from_affid( int $affid ) {
    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliates';
    $query = "SELECT uid FROM $table WHERE id = $affid";
    $wpid = $wpdb->get_var( $query );

    if ( empty( $wpid ) ) {
      return false;
    }

    return self::from_id( $wpid );
  }


  public function affid() {
    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliates';
    $query = "SELECT id FROM $table WHERE uid = {$this->id}";
    $affid = $wpdb->get_var( $query );

    $affid = $affid ? (int) $affid : 0;

    return $affid;
  }


  public function get_sponsor() {
    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
    $query = "SELECT affiliate_id FROM $table WHERE referral_wp_uid = $this->id";
    $sponsor_affid = $wpdb->get_var( $query );

    if ( empty( $sponsor_affid ) ) {
      return false;
    }

    return self::from_affid( $sponsor_affid );
  }

  
}