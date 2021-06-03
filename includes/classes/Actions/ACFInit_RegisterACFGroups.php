<?php declare(strict_types=1);

namespace MOS\Affiliate\Actions;

use \MOS\Affiliate\AbstractAction;
use \MOS\Affiliate\Commands\LoadACFGroupCommand;

class ACFInit_RegisterACFGroups extends AbstractAction {

  const ACF_DIR_PATH = \MOS\Affiliate\PLUGIN_DIR . 'includes/json/acf/';

  protected $hook = 'acf/init';

  public function handler(): void {
    $dir = new \DirectoryIterator( self::ACF_DIR_PATH );
    
    foreach ( $dir as $file ) {
      if ( $file->getExtension() == 'json' ) {
        $json = file_get_contents( $file->getPathname() );
        $field_group = json_decode( $json, true );
        $load_acf_group_command = new LoadACFGroupCommand( $field_group );
        $load_acf_group_command->execute();
      }
    }
  }

}
