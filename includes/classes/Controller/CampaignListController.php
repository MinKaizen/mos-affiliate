<?php

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;
use MOS\Affiliate\Database;

class CampaignListController extends Controller {

  protected $variables = [
      'campaigns',
    ];


  protected function campaigns() {
    $db = new Database();
    $campaigns = $db->get_campaigns();
    return $campaigns;
  }

}