<?php declare(strict_types=1);

namespace MOS\Affiliate;

class HtmlTable {

  private $body = [];
  private $headers = [];
  private $id = '';
  private $class = '';
  

  public function __construct( array $body, array $options = [] ) {
    $this->body = $body;
    $this->headers = array_keys( reset( $body ) );
    
    foreach( $this->headers as &$header ) {
      $header = snake_to_proper_case( $header );
    }    

    $props = [
      'id',
      'class',
    ];
    foreach( $props as $prop ) {
      $this->$prop = empty( $options[$prop] ) ? $this->$prop : $options[$prop];
    }
  }


  public function rows(): array {
    return $this->body;
  }


  public function headers(): array {
    return $this->headers;
  }


  public function id(): string {
    return $this->id;
  }


  public function class(): string {
    return $this->class;
  }


}