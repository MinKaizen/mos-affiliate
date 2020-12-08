<?php declare(strict_types=1);

namespace MOS\Affiliate\Test;

use MOS\Affiliate\Test;
use MOS\Affiliate\Controller\CampaignReport;

class CampaignReportTest extends Test {

  private $vars = [];


  protected function _before(): void {
    $controller = new CampaignReport();
    $this->vars = $controller->get_vars();
  }


  public function test_get_campaigns_for_logged_out_user(): void {
    $this->assert_equal( [], $this->vars['campaigns'] );
  }


  public function test_get_headers_for_logged_out_user(): void {
    $this->assert_equal( [], $this->vars['headers'] );
  }

}