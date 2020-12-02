<?php declare(strict_types=1);

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;
use MOS\Affiliate\User;

class CampaignReport extends Controller {

  const EMPTY_CAMPAIGN_NAME = '(no campaign)';

  private $campaigns = [];
  private $user;
  private $affid = 0;


  public function __construct() {
    $this->user = User::current();
    $this->affid = $this->user->get_affid();
    
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


  public function export_campaign_names(): array {
    $campaign_names = [
      self::EMPTY_CAMPAIGN_NAME,
    ];
    $campaign_names = array_merge( $campaign_names, User::current()->get_campaigns() );
    return $campaign_names;
  }


}