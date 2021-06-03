<?php declare(strict_types=1);

namespace MOS\Affiliate\Commands;

use \MOS\Affiliate\AbstractCommand;

class LoadACFGroupCommand extends AbstractCommand {

  private $field_group;

  public function __construct( array $field_group ) {
    $this->field_group = $field_group;
  }

  public function execute(): void {
    if ( function_exists( 'acf_add_local_field_group' ) ) {
      \acf_add_local_field_group( $this->field_group );
    }
  }

}