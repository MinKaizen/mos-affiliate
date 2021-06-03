<?php declare(strict_types=1);

namespace MOS\Affiliate\Actions;

use \MOS\Affiliate\AbstractAction;
use \MOS\Affiliate\AbstractCommand;

class ExecuteCommand extends AbstractAction {

  protected $hook = 'execute_command';

  public function handler( AbstractCommand $command ): void {
    $command->execute();
  }

}