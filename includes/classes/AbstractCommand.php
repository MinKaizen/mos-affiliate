<?php declare(strict_types=1);

namespace MOS\Affiliate;

abstract class AbstractCommand {

  private $action_hook = 'execute_command';

  abstract function execute(): void;

  public function execute_async(): void {
    wp_schedule_single_event( time(), $this->action_hook, [$this] );
  }

}