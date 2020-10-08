<?php

/**
 * Performs database setters and getters
 */

class MosAffiliateDb {

  public static function instance() {
    $new_instance = new self;
    return $new_instance;
  }


  public function get_campaign_data() {
    global $wpdb;

    // Get affid of current user
    $affid = $this->get_affid();

    // Check if affid is valid
    if ( $affid === false ) {
      return false;
    }

    // Perform SQL lookup
    $table = $wpdb->prefix.'uap_campaigns';
    $query = "SELECT `name`, `visit_count` as clicks, `unique_visits_count` as unique_clicks, `referrals` FROM $table WHERE affiliate_id = $affid";
    $campaign_data = $wpdb->get_results( $query, );

    // Check if campaign data is valid
    if ( empty( $campaign_data ) ) {
      return false;
    }

    return $campaign_data;
  }
  

  public function get_campaigns() {
    global $wpdb;

    // Get wpid of current user
    $affid = $this->get_affid();

    // If affid not found, return false
    if ( empty( $affid ) ) {
      return false;
    }

    // Perform SQL lookup
    $table = $wpdb->prefix.'uap_campaigns';
    $query = "SELECT DISTINCT name FROM $table WHERE affiliate_id = $affid";
    $campaigns = $wpdb->get_col( $query );

    // If SQL failed, return false
    if ( empty($campaigns) ) {
      return false;
    }

    return $campaigns;
  }


  public function get_affid() {
    global $wpdb;

    // Get wpid of current user
    $wpid = $this->get_wpid();

    // If wpid no found, return false
    if ( empty( $wpid ) ) {
      return false;
    }

    // Perform SQL lookup
    $table = $wpdb->prefix.'uap_affiliates';
    $query = "SELECT id FROM $table WHERE uid = $wpid LIMIT 1";
    $affid = $wpdb->get_var($query);

    // If lookup failed, return false
    if ( empty( $affid ) ) {
      return false;
    }

    return $affid;


  }


  public function get_wpid() {
    $wpid = get_current_user_id();

    if ( empty( $wpid ) ) {
      return false;
    }

    return $wpid;
  }


}