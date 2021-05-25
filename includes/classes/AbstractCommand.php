<?php declare(strict_types=1);

namespace MOS\Affiliate;

abstract class AbstractCommand {

  abstract function execute(): void;

}