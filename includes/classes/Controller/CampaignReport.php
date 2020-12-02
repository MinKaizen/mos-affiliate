<?php declare(strict_types=1);

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;
use MOS\Affiliate\User;

class CampaignReport extends Controller {

  const EMPTY_CAMPAIGN_NAME = '(no campaign)';

  private $campaigns = [];
  private $user;
  private $affid = 0;
  private $referrals = [];


  public function __construct() {
    $this->user = User::current();
    $this->affid = $this->user->get_affid();
    $this->referrals = $this->user->get_referrals();
    
    $campaigns = $this->get_campaign_data();

    // Add empty partners column to campaigns
    foreach ( $campaigns as &$campaign ) {
      $campaign['partners'] = 0;
    }
    
    // Get a list of referrals
    $user = User::current();
    $referrals = $user->get_referrals();

    // Count partners
    foreach ( $referrals as $referral ) {
      if ( $referral->is_partner() ) {
        if ( empty( $referral->get_campaign() ) ) {
          continue;
        }
        $campaigns[$referral->get_campaign()]['partners']++;
      }
    }

    $this->campaigns = $campaigns;
  }


  protected function export_campaigns(): array {
    return $this->campaigns;
  }


  protected function export_headers(): array {
    if ( empty( $this->campaigns ) ) {
      return [];
    }
    $first_element = reset( $this->campaigns );
    $headers = empty( $first_element ) ? [] : array_keys( $first_element );
    return $headers;
  }


  private function get_campaign_data(): array {
    global $wpdb;

    // Get affid of current user
    $affid = User::current()->get_affid();

    // Check if affid is valid
    if ( empty( $affid ) ) {
      return [];
    }

    // Perform SQL lookup
    $table = $wpdb->prefix.'uap_campaigns';
    $query = "SELECT `name`, `visit_count` as clicks, `unique_visits_count` as unique_clicks, `referrals` FROM $table WHERE affiliate_id = $affid";
    $campaign_data = $wpdb->get_results( $query, \ARRAY_A );

    // Check if campaign data is valid
    if ( empty( $campaign_data ) ) {
      return [];
    }

    foreach( $campaign_data as $index => $campaign ) {
      $campaign_data[$campaign['name']] = $campaign;
      unset( $campaign_data[$index] );
    }

    return $campaign_data;
  }




  private function get_campaign_clicks_and_refs(): array {
    if ( empty( $this->affid ) ) {
      return [];
    }

    global $wpdb;
    $table = $wpdb->prefix.'uap_campaigns';
    $query = "SELECT `name`, `unique_visits_count` as clicks, `referrals` FROM $table WHERE affiliate_id = $this->affid";
    $campaign_data = (array) $wpdb->get_results( $query, \ARRAY_A );

    if ( empty( $campaign_data ) ) {
      return [];
    }

    // Set index to campaign name
    foreach( $campaign_data as $index => $campaign ) {
      $campaign_data[$campaign['name']] = $campaign;
      unset( $campaign_data[$index] );
    }

    return $campaign_data;
  }


  private function add_empty_row( array $campaign ): array {
    $modified_campaign = $campaign;
    $modified_campaign[self::EMPTY_CAMPAIGN_NAME] = [
      'clicks' => $this->get_empty_campaign_clicks(),
      'referrals' => $this->get_empty_campaign_referrals(),
    ];
    return $modified_campaign;
  }


  private function get_empty_campaign_clicks(): int {
    global $wpdb;
    $table = $wpdb->prefix . 'uap_visits';
    $query = "SELECT COUNT(`campaign_name`) FROM $table WHERE `campaign_name` = '' AND `affiliate_id` = $this->affid";
    $result = (int) $wpdb->get_var( $query );
    return $result;
  }


  private function get_empty_campaign_referrals(): int {
    global $wpdb;
    $table = $wpdb->prefix . 'uap_referrals';
    $query = "SELECT COUNT(`campaign`) FROM $table WHERE `campaign` = '' AND `affiliate_id` = $this->affid";
    $result = (int) $wpdb->get_var( $query );
    return $result;
  }


  private function append_partners( array $campaigns ): array {
    foreach ( $campaigns as &$campaign ) {
      $campaign['partners'] = 0;
    }

    foreach ( $this->referrals as $user ) {
      if ( $user->is_partner() ) {
        $campaign_name = $user->get_campaign();
        $campaign_name = $campaign_name ? $campaign_name : self::EMPTY_CAMPAIGN_NAME;
        if ( isset( $campaigns[$campaign_name]['partners'] ) ) {
          $campaigns[$campaign_name]['partners']++;
        }
      }
    }

    return $campaigns;
  }


  private function append_commissions( array $campaigns ): array {
    return $campaigns;
  }


}