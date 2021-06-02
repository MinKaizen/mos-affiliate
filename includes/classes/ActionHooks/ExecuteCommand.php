<?php declare(strict_types=1);

namespace MOS\Affiliate\ActionHooks;

use \MOS\Affiliate\ActionHook;
use \MOS\Affiliate\AbstractCommand;

class ExecuteCommandAsync extends ActionHook {

  protected $hook = 'execute_command';

  public function handler( AbstractCommand $command ): void {
    $command->execute();
  }

}