<?php declare(strict_types=1);

namespace MOS\Affiliate\CliCommand;

use MOS\Affiliate\CliCommand;
use MOS\Affiliate\Activator;

class ReactivateCliCommand extends CliCommand {

  protected $command = 'reactivate';

  public function run( array $pos_args, array $assoc_args ): void {
    Activator::activate();
  }


}