<?php declare(strict_types=1);

namespace MOS\Affiliate;

use MOS\Affiliate\CliCommand\ClearTestDataCliCommand;
use MOS\Affiliate\CliCommand\TestCliCommand;
use MOS\Affiliate\CliCommand\ReactivateCliCommand;

class CLI {
  
  public function test( array $pos_args, array $assoc_args ): void {
    $command = new TestCliCommand();
    $command->run( $pos_args, $assoc_args );
  }


  public function clear_test_data( array $pos_args, array $assoc_args  ): void {
    $command = new ClearTestDataCliCommand();
    $command->run( $pos_args, $assoc_args );
  }


  public function reactivate( array $pos_args, array $assoc_args ): void {
    $command = new ReactivateCliCommand();
    $command->run( $pos_args, $assoc_args );
  }

}