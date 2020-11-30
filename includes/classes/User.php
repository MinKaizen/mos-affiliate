<?php declare(strict_types=1);

namespace MOS\Affiliate;

use MOS\Affiliate\Mis;

class User extends \WP_User {

  
  public static function current(): self {
    $wpid = \get_current_user_id();
    $current_user = self::from_id( $wpid );
    $current_user = apply_filters( 'mos_current_user', $current_user );
    return $current_user;
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
    $wpid = $wpid ? (int) $wpid : 0;

    return self::from_id( $wpid );
  }


  public static function from_username( string $username ): self {
    global $wpdb;

    $table = $wpdb->prefix . 'users';
    $query = "SELECT id FROM $table WHERE user_login = '$username'";
    $wpid = $wpdb->get_var( $query );
    $wpid = $wpid ? (int) $wpid : 0;

    return self::from_id( $wpid );
  }


  public static function id_exists( int $id ): bool {
    $user = \get_user_by( 'id', $id );
    return ! empty( $user );
  }


  public static function affid_exists( int $affid ): bool {
    global $wpdb;
    $table = $wpdb->prefix . 'uap_affiliates';
    $query = "SELECT id FROM $table WHERE id = $affid LIMIT 1";
    $result = (int) $wpdb->get_var( $query );
    $affid_exists = $result == $affid;
    return $affid_exists;
  }


  public function sponsor(): self {
    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
    $query = "SELECT affiliate_id FROM $table WHERE referral_wp_uid = $this->ID";
    $sponsor_affid = $wpdb->get_var( $query );
    $sponsor_affid = $sponsor_affid ? (int) $sponsor_affid : 0;

    $sponsor = self::from_affid( $sponsor_affid );
    $sponsor = \apply_filters( 'mos_sponsor', $sponsor, $this->ID );
    return $sponsor;
  }


  public function is_empty(): bool {
    $is_empty = empty( $this->ID );
    return $is_empty;
  }


  public function get_wpid(): int {
    $wpid = $this->ID ? $this->ID : 0;
    return $wpid;
  }


  public function get_affid(): int {
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
    $value = $mis->exists() ? $this->get( $mis->get_meta_key() ) : '';
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
    $qualifies = $mis->exists() ? $this->has_cap( $mis->get_cap() ) : false;
    return $qualifies;
  }


  public function db_insert(): void {
    if ( ! empty( $this->ID ) && self::id_exists( $this->ID ) ) {
      return;
    }

    if ( empty( $this->user_login ) ) {
      return;
    }

    if ( empty( $this->user_pass ) ) {
      return;
    }

    $new_user_id = \wp_insert_user( $this );
    if ( is_int( $new_user_id ) && !empty( $new_user_id ) ) {
      $this->ID = $new_user_id;
    }
  }


  public function db_delete(): void {
    $is_affiliate = ! empty( $this->get_affid() );
    $has_sponsor = ! $this->sponsor()->is_empty();
    $is_real_user = ! $this->is_empty();

    if ( $is_real_user ) {
      \wp_delete_user( $this->ID );
    }

    if ( $has_sponsor ) {
      $this->db_remove_sponsor();
    }

    if ( $is_affiliate ) {
      $this->db_unregister_affiliate();
    }
  }


  public function db_add_sponsor( User $sponsor ): void {
    $already_has_sponsor = ! $this->sponsor()->is_empty();
    if ( $already_has_sponsor ) {
      return;
    }

    $sponsor_affid = $sponsor->get_affid();
    if ( empty( $sponsor_affid ) ) {
      return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
    $columns = [
      'affiliate_id' => $sponsor_affid,
      'referral_wp_uid' => $this->ID,
    ];
    $formats = [
      'affiliate_id' => '%d',
      'referral_wp_uid' => '%d',
    ];
    $wpdb->insert( $table, $columns, $formats );
  }


  private function db_remove_sponsor(): void {
    global $wpdb;
    $table = $wpdb->prefix . 'uap_affiliate_referral_users_relations';
    $where = ['referral_wp_uid' => $this->ID];
    $formats = ['referral_wp_uid' => '%d'];
    $wpdb->delete( $table, $where, $formats );
  }


  private function db_unregister_affiliate(): void {
    global $wpdb;
    $table = $wpdb->prefix . 'uap_affiliates';
    $where = ['uid' => $this->ID];
    $formats = ['uid' => '%d'];
    $wpdb->delete( $table, $where, $formats );
  }


}