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
    $campaigns = $this->get_campaign_clicks_and_refs();
    $campaigns = $this->add_empty_row( $campaigns );
    $campaigns = $this->append_partners( $campaigns );
    $campaigns = $this->append_commissions( $campaigns );
    $campaigns = $this->append_epc( $campaigns );
    return $campaigns;
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
    $campaign[self::EMPTY_CAMPAIGN_NAME] = [
      'name' => self::EMPTY_CAMPAIGN_NAME,
      'clicks' => $this->get_empty_campaign_clicks(),
      'referrals' => $this->get_empty_campaign_referrals(),
    ];
    return $campaign;
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
      $campaign['EPC'] = (float) $campaign['commissions'] / (float) $campaign['clicks'];
      $campaign['EPC'] = is_nan($campaign['EPC']) ? 0 : $campaign['EPC'];
    }

    return $campaigns;
  }


}