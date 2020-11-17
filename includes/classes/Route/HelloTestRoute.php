<?php

namespace MOS\Affiliate\Route;

use MOS\Affiliate\AbstractTestRoute;

class HelloTestRoute extends AbstractTestRoute {

  protected $route = 'hello';

  public function serve(): void  {
    echo "Hello! This is a Test Route!\n";
  }

}