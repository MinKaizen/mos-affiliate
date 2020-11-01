<?php

namespace MOS\Affiliate;

class CampaignListController extends MosAffiliateController {

  protected $variables = [
      'campaigns',
    ];


  protected function campaigns() {
    $db = MosAffiliateDb::instance();
    $campaigns = $db->get_campaigns();
    return $campaigns;
  }

}