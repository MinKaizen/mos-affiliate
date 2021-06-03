<?php declare(strict_types=1);

namespace MOS\Affiliate\Actions;

use \MOS\Affiliate\AbstractAction;
use \MOS\Affiliate\User;
use function \current_user_can;
use function \add_filter;

class InjectUser extends AbstractAction {

  protected $hook = 'template_redirect';

  function handler(): void {
    if ( empty( $_GET['set_user'] ) ) {
      return;
    }

    if ( !current_user_can( 'activate_plugins' ) ) {
      return;
    }

    $user = User::from_username( $_GET['set_user'] );

    if ( $user->exists() ) {
      // Used in User::current()
      add_filter( 'mos_current_user', function() use ($user) {
        return $user;
      }, 0 );
      // Used in mos-sage theme
      add_filter( 'mos_current_user_id', function() use ($user) {
        return $user->get_wpid();
      }, 0 );
    }
  }

}