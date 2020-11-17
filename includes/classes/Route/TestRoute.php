<?php

namespace MOS\Affiliate\Route;

use MOS\Affiliate\AbstractRoute;

class TestRoute extends AbstractRoute {

  protected $route = 'test';

  public function serve(): void {
    echo "Hello world! abstract this time";
  }

}