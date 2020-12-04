<?php declare(strict_types=1);

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;
use MOS\Affiliate\User;

use function MOS\Affiliate\format_currency;

class CampaignReport extends Controller {

  const EMPTY_CAMPAIGN_NAME = '(none)';

  private $campaigns = [];
  private $user;
  private $affid = 0;
  private $referrals = [];
  private $commissions = [];


  public function __construct() {
    $this->user = User::current();
    $this->affid = $this->user->get_affid();
    $this->referrals = $this->user->get_referrals();
    $this->commissions = $this->user->get_commissions();
    $this->campaigns = $this->get_campaigns();
  }


  protected function export_headers(): array {
    if ( empty( $this->campaigns ) ) {
      return [];
    }
    $first_element = reset( $this->campaigns );
    $headers = empty( $first_element ) ? [] : array_keys( $first_element );
    return $headers;
  }


  public function export_campaigns(): array {
    return $this->campaigns;
  }


  private function get_campaigns(): array {
    $campaigns = $this->get_campaign_clicks();
    $campaigns = $this->append_referrals( $campaigns );
    $campaigns = $this->append_partners( $campaigns );
    $campaigns = $this->append_commissions( $campaigns );
    $campaigns = $this->append_epc( $campaigns );
    return $campaigns;
  }


  private function get_campaign_clicks(): array {
    if ( empty( $this->affid ) ) {
      return [];
    }

    global $wpdb;
    $table = $wpdb->prefix.'uap_visits';
    $query = "SELECT campaign_name as name, count(DISTINCT ip) as clicks FROM $table WHERE affiliate_id = $this->affid GROUP BY campaign_name";
    $campaign_data = (array) $wpdb->get_results( $query, \ARRAY_A );

    if ( empty( $campaign_data ) ) {
      return [];
    }

    // Set index to campaign name
    foreach( $campaign_data as $numbererd_index => $campaign ) {
      $named_index = empty( $campaign['name'] ) ? self::EMPTY_CAMPAIGN_NAME : $campaign['name'];
      $campaign_data[$named_index] = $campaign;
      unset( $campaign_data[$numbererd_index] );
    }

    return $campaign_data;
  }


  private function append_referrals( array $campaigns ): array {
    global $wpdb;
    $table = $wpdb->prefix . 'uap_referrals';
    $query = "SELECT campaign as name, count(DISTINCT refferal_wp_uid) as referrals FROM $table WHERE affiliate_id = $this->affid GROUP BY campaign";
    $results = $wpdb->get_results( $query, \ARRAY_A );

    if ( empty( $results ) ) {
      return $campaigns;
    }

    // Set index to campaign name
    foreach( $results as $numbererd_index => $result ) {
      $named_index = empty( $result['name'] ) ? self::EMPTY_CAMPAIGN_NAME : $result['name'];
      $results[$named_index] = $result;
      unset( $results[$numbererd_index] );
    }
    
    // Merge arrays
    foreach ( $campaigns as $campaign_name => &$campaign ) {
      $campaign['referrals'] = empty( $results[$campaign_name] ) ? 0 : (int) $results[$campaign_name]['referrals'];
    }

    return $campaigns;
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
    foreach ( $campaigns as &$campaign ) {
      $campaign['commissions'] = 0;
    }

    foreach ( $this->commissions as $commission ) {
      $campaign_name = $commission->get_campaign();
      $campaign_name = $campaign_name ? $campaign_name : self::EMPTY_CAMPAIGN_NAME;
      if ( isset( $campaigns[$campaign_name]['commissions'] ) ) {
        $campaigns[$campaign_name]['commissions'] += $commission->get_amount();
      }
    }

    foreach ( $campaigns as &$campaign ) {
      $campaign['commissions'] = format_currency( $campaign['commissions'], 0 );
    }

    return $campaigns;
  }


  private function append_epc( array $campaigns ): array {
    foreach ( $campaigns as $campaign ) {
      if ( !isset( $campaign['clicks'] ) ) {
        return [];
      }
      if ( !isset( $campaign['commissions'] ) ) {
        return [];
      }
    }

    foreach ( $campaigns as &$campaign ) {
      $campaign['EPC'] = (float) $campaign['clicks'] == 0.0 ? 0.0 : (float) $campaign['commissions'] / (float) $campaign['clicks'];
      $campaign['EPC'] = format_currency( $campaign['EPC'] );
    }

    return $campaigns;
  }


}