<?php

namespace MOS\Affiliate\Route;

use MOS\Affiliate\AbstractRoute;

class TestRoute extends AbstractRoute {

  protected $base = 'test';
  protected $route = 'hello';

  public function serve(): void {
    echo "Hello world!";
  }

}