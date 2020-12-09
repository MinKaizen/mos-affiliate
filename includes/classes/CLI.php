<?php declare(strict_types=1);

namespace MOS\Affiliate;

use MOS\Affiliate\CliCommand\DeleteTestDataCliCommand;
use MOS\Affiliate\CliCommand\TestCliCommand;
use MOS\Affiliate\CliCommand\ReactivateCliCommand;

class CLI {
  
  public function test( array $pos_args, array $assoc_args ): void {
    $command = new TestCliCommand();
    $command->run( $pos_args, $assoc_args );
  }


  public function delete_test_data( array $pos_args, array $assoc_args  ): void {
    $command = new DeleteTestDataCliCommand();
    $command->run( $pos_args, $assoc_args );
  }


  public function reactivate( array $pos_args, array $assoc_args ): void {
    $command = new ReactivateCliCommand();
    $command->run( $pos_args, $assoc_args );
  }

}