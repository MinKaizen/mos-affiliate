<?php

namespace MOS\Affiliate\Controller;

use MOS\Affiliate\Controller;
use MOS\Affiliate\Database;

class CampaignList extends Controller {


  protected function export_campaigns() {
    $db = new Database();
    $campaigns = $db->get_campaigns();
    return $campaigns;
  }


}