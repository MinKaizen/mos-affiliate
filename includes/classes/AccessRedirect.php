<?php

namespace MOS\Affiliate;

class AccessRedirect {

  protected $tag;
  protected $redirect_url;
  protected $cap;


  public function register(): void {
    \add_action( 'template_redirect', [ $this, 'maybe_redirect' ] );
  }


  public function maybe_redirect(): void {
    if ( !\has_tag( $this->tag ) ) {
      return;
    };

    $user = \wp_get_current_user();
    $has_access = $user->has_cap( $this->cap );
    if ( ! $has_access ) {
      \wp_redirect( \home_url( $this->redirect_url ) );
      exit;
    }
  }


  public function get_tag() {
    return $this->tag;
  }

  
  public function get_redirect_url() {
    return $this->redirect_url;
  }


  public function get_cap() {
    return $this->cap;
  }


}