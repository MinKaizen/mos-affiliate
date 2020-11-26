<?php

namespace MOS\Affiliate;

use \Exception;
use \WP_CLI;
use \MOS\Affiliate\User;

use function \wp_delete_user;
use function \wp_insert_user;
use function \MOS\Affiliate\ranstr;
use function \add_filter;
use function \remove_filter;
use function \remove_all_filters;

class Test {

  const CURRENT_USER_HOOK = 'mos_current_user';

  protected $_user_ids_to_delete;
  protected $_injected_user;


  public function run(): void {
    foreach( $this->get_test_methods() as $method ) {
      $this->run_method( $method );
    }
    $this->_clean_up();
  }


  protected final function _clean_up() {
    $this->delete_test_users();
    $this->unset_user();
  }


  private function get_test_methods(): array {
    $methods = get_class_methods( get_class( $this ) );
    $test_methods = [];

    foreach( $methods as $method ) {
      if ( strpos( $method, 'test_'  ) === 0 ) {
        $test_methods[] = $method;
      }
    }

    return $test_methods;
  }


  private function run_method( string $method ): void {
    $current_test = $this->get_class_name() . "::" . $method;
    WP_CLI::debug( "Running $current_test", 'mosa test' );
    $this->{$method}();
    WP_CLI::line( WP_CLI::colorize( "%g✔%n $current_test" ) );
  }


  private function get_class_name( bool $verbose=false ): string {
    $class_name = get_class( $this );

    if ( $verbose === false ) {
      // Get class name stub
      foreach ( explode( '\\', $class_name ) as $part ) {
        $class_name_short = $part;
      }
      $class_name = $class_name_short;
    }

    return $class_name;
  }


  protected function assert_is_int( $var, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = is_int( $var );
    $data['var'] = $var;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_greater_than( $var1, $var2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $var1 > $var2;
    $data['comparison'] = "$var1 > $var2";
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_greater_than_or_equal( $var1, $var2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $var1 >= $var2;
    $data['comparison'] = "$var1 >= $var2";
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_less_than( $var1, $var2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $var1 < $var2;
    $data['comparison'] = "$var1 < $var2";
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_less_than_or_equal( $var1, $var2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $var1 <= $var2;
    $data['comparison'] = "$var1 <= $var2";
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_is_array( $var, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = is_array( $var );
    $data['var'] = $var;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_equal( $value1, $value2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 == $value2;
    $data['value1'] = $value1;
    $data['value2'] = $value2;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_equal_strict( $value1, $value2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 === $value2;
    $data['value1'] = $value1;
    $data['value2'] = $value2;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_not_equal( $value1, $value2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 != $value2;
    $data['value1'] = $value1;
    $data['value2'] = $value2;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_not_equal_strict( $value1, $value2, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $value1 !== $value2;
    $data['value1'] = $value1;
    $data['value2'] = $value2;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_true( $expression, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $expression == true;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_true_strict( $expression, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $expression === true;
    $data['expression'] = $expression;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_false( $expression, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $expression == false;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_false_strict( $expression, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $expression === false;
    $data['expression'] = $expression;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_string_contains( string $haystack, string $needle, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = ( strpos( $haystack, $needle ) !== false );
    $data['haystack'] = $haystack;
    $data['needle'] = $needle;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_has_key( array $array, $key, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = array_key_exists( $key, $array );
    $data['array'] = $array;
    $data['key'] = $key;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_instanceof( $instance, string $class, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = $instance instanceof $class;
    $data['instance'] = $instance;
    $data['class'] = $class;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_class_exists( string $class_name, ...$data ): void {
    $assertion = __FUNCTION__;
    $condition = class_exists( $class_name );
    $data['class_name'] = $class_name;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert_db_table_exists( string $table_without_prefix, ...$data ): void {
    global $wpdb;
    $assertion = __FUNCTION__;
    $table = $wpdb->prefix . $table_without_prefix;
    $query = "SHOW TABLES LIKE '$table'";
    $result = $wpdb->get_var( $query );
    $condition = $result == $table;
    $data['query'] = $query;
    $data['result'] = $result;
    $this->assert( $condition, $data, $assertion );
  }


  protected function assert( $condition, $data=[], string $assertion ): void {
    if ( $condition ) {
      return;
    }

    $e = new Exception();
    $trace_string = $e->getTraceAsString();
    $trace_formatted = $this->format_trace( $trace_string );
    
    WP_CLI::line( $trace_formatted );

    $this->print_yaml( $data );
    $this->print_error( $assertion );
  }

  
  protected function print_error( string $message ): void {
    $colorized_message = WP_CLI::colorize( '%1%W' . $message . '%n%N');
    WP_CLI::error( $colorized_message );
  }
  
  
  protected function db_notice( string $message ): void {
    $colorized_message = WP_CLI::colorize( '%b✦%n ' . $message);
    WP_CLI::debug( $colorized_message, 'mosa db notice' );
  }


  protected function print_yaml( $original ): void {
    if ( empty( $original ) ) {
      WP_CLI::warning( "No debug data passed" );
      return;
    }
    $debug_dump = [
      [
        'debug_dump' => $original,
      ],
    ];

    WP_CLI\Utils\format_items( 'yaml', $debug_dump, 'debug_dump' );
  }


  protected function format_trace( string $original ): string {
    $exploded = explode("\n", print_r( $original, true));

    // Cut and colorize
    foreach ( $exploded as &$line ) {
      $line = preg_replace( '/(.*)(\/[a-zA-Z0-9]+\.php)(\(\d+\))(.*)/', '%5%W$2%6%W$3%n%N$4', $line );
      $line = WP_CLI::colorize( $line );
    }

    $formatted = 'Stack trace:' . PHP_EOL;

    foreach ( $exploded as $line ) {
      if ( strpos( $line, "[internal function]" ) === false ) {
        $formatted .= $line . PHP_EOL;
      } else {
        break;
      }
    }

    return $formatted;
  }


  protected function create_test_user( array $user_data=[] ): User {
    if ( empty( $user_data['user_login'] ) ) {
      $user_data['user_login'] = ranstr();
    }
    
    if ( empty( $user_data['user_pass'] ) ) {
      $user_data['user_pass'] = ranstr();
    }

    $id = wp_insert_user( $user_data );
    $this->assert_is_int( $id, "wp_insert_user should return user ID on success", $user_data );
    $this->_user_ids_to_delete[] = $id;
    $this->db_notice( "user created: $id" );
    
    $user = User::from_id( $id );
    return $user;
  }


  protected function delete_test_users(): void {
    if ( empty( $this->_user_ids_to_delete ) ) {
      return;
    }

    foreach ( $this->_user_ids_to_delete as $id ) {
      wp_delete_user( $id );
      $this->db_notice( "user deleted: $id" );
    }
  }


  /**
   * Used as a callback for add_filter only
   * Do not call directly!
   */
  public final function _get_injected_user(): ?User {
    return $this->_injected_user;
  }


  protected final function set_user( User $user ): void {
    $this->_injected_user = $user;
    remove_all_filters( self::CURRENT_USER_HOOK );
    add_filter( self::CURRENT_USER_HOOK, [$this, '_get_injected_user'] );
    $this->db_notice( "current user filter added: {$user->ID}" );
  }


  protected final function unset_user(): void {
    if ( empty( $this->_injected_user ) ) {
      return;
    }

    $remove_success = remove_filter( self::CURRENT_USER_HOOK, [$this, '_get_injected_user'] );
    if ($remove_success) {
      $this->db_notice("current user filter removed: {$this->_injected_user->ID}");
    }
  }


}