<?php

class CampaignReportController extends MosAffiliateController {

  protected $variables = [
    'campaigns',
    'headers',
  ];


  protected function campaigns() {
    $db = MosAffiliateDb::instance();
    $campaigns = $db->get_campaign_data();
    return $campaigns;
  }


  protected function headers() {
    $headers = array_keys((array)$this->campaigns()[0]);
    return $headers;
  }

}