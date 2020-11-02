<?php

namespace MOS\Affiliate;

class User extends \WP_User {

  
  public static function current() {
    $wpid = \get_current_user_id();
    $user = $wpid ? new User( $wpid ) : false;
    return $user;
  }


  public function affid() {
    global $wpdb;

    $table = $wpdb->prefix . 'uap_affiliates';
    $query = "SELECT id FROM $table WHERE uid = {$this->id}";
    $affid = $wpdb->get_var( $query );

    $affid = $affid ? (int) $affid : 0;

    return $affid;
  }

}